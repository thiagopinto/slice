<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Progress } from '@/components/ui/progress';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useForm } from '@inertiajs/vue3';

// Props
const props = defineProps({
    endpoint: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'POST'
    },
    label: String,
    accept: String,
    debug: {
        type: Boolean,
        default: false,
        required: false
    }
});

// Estado
const file = ref<File | null>(null);
const uploadProgress = ref(0);
const isLoading = ref(false);
const error = ref<string | null>(null);

// Envia o arquivo via Inertia (ou axios, se preferir)
const form = useForm({
    file: null as File | null,
});

const handleSubmit = async () => {
    if (!file.value) {
        error.value = 'Selecione um arquivo.';
        return;
    }

    isLoading.value = true;
    form.file = file.value;
    let url = props.endpoint;

    url = props.debug
        ? `${props.endpoint}?XDEBUG_SESSION=VSCODE`
        : props.endpoint;

    form.post(url, {
        forceFormData: true,
        onProgress: (progress) => {
            uploadProgress.value = progress?.percentage ?? 0;
        },
        onSuccess: () => {
            error.value = null;
        },
        onError: (errors) => {
            error.value = errors.file || 'Erro no upload.';
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ label || 'Upload de Arquivo' }}</CardTitle>
        </CardHeader>
        <CardContent>
            <Input type="file" @change="(e: Event) => (file = (e.target as HTMLInputElement).files?.[0] || null)"
                :accept="accept" />

            <Progress v-if="isLoading" :model-value="uploadProgress" class="mt-4" />

            <Button @click="handleSubmit" :disabled="!file || isLoading" class="mt-4">
                {{ isLoading ? 'Enviando...' : 'Enviar' }}
            </Button>

            <p v-if="error" class="mt-2 text-sm text-red-500">{{ error }}</p>
        </CardContent>
    </Card>
</template>
