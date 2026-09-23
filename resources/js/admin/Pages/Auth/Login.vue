<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { LogIn } from 'lucide-vue-next'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit(): void {
    form.post(route('admin.login.attempt'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
  <Head :title="$t('auth.title')" />

  <div class="flex min-h-screen">
    <!-- Фирменная половина экрана: та же навы-гамма, что на витрине -->
    <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden p-12 text-white lg:flex" style="background: linear-gradient(135deg, #0d1636 0%, #162456 45%, #2a3f7c 100%)">
      <span
        class="pointer-events-none absolute inset-0 opacity-[0.06]"
        style="background-image: url('/brand/icon_pattern.svg'); background-size: 180px"
        aria-hidden="true"
      />

      <img src="/brand/logo_horizontal_white.svg" alt="GHEKATEX" class="relative h-8 w-auto" width="200" height="34">

      <div class="relative">
        <p class="text-sm uppercase tracking-[0.18em] text-[#dcc08c]">{{ $t('auth.title') }}</p>
        <p class="mt-4 max-w-md text-2xl font-light leading-snug">{{ $t('auth.subtitle') }}</p>
      </div>

      <p class="relative text-xs text-white/40">Ghekatex Group SRL</p>
    </div>

    <div class="flex w-full items-center justify-center px-6 py-12 lg:w-1/2">
      <div class="w-full max-w-sm">
        <img src="/brand/logo_horizontal_filled.svg" alt="GHEKATEX" class="mb-10 h-7 w-auto lg:hidden" width="180" height="30">

        <h1 class="text-2xl font-semibold text-steel-900">{{ $t('auth.title') }}</h1>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
          <div>
            <label class="label" for="email">{{ $t('auth.email') }}</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autocomplete="username"
              class="field"
              :class="{ 'field-invalid': form.errors.email }"
            >
            <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="label" for="password">{{ $t('auth.password') }}</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              autocomplete="current-password"
              class="field"
              :class="{ 'field-invalid': form.errors.password }"
            >
            <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
          </div>

          <label class="flex items-center gap-2.5 text-sm text-steel-600">
            <input v-model="form.remember" type="checkbox" class="accent-[#162456]">
            {{ $t('auth.remember') }}
          </label>

          <button type="submit" class="btn btn-primary w-full" :disabled="form.processing">
            <LogIn :size="16" />
            {{ $t('auth.submit') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
