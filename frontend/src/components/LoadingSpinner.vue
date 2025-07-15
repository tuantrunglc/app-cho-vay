<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="relative">
      <!-- Spinner -->
      <div 
        class="animate-spin rounded-full border-4 border-gray-200"
        :class="[
          sizeClass,
          `border-t-${color}-600`
        ]"
      ></div>
      
      <!-- Center dot -->
      <div 
        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rounded-full"
        :class="[
          dotSizeClass,
          `bg-${color}-600`
        ]"
      ></div>
    </div>
    
    <!-- Loading text -->
    <div v-if="text" class="ml-3">
      <p class="text-gray-600 font-medium">{{ text }}</p>
      <p v-if="subtext" class="text-sm text-gray-500">{{ subtext }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  color: {
    type: String,
    default: 'primary'
  },
  text: {
    type: String,
    default: ''
  },
  subtext: {
    type: String,
    default: ''
  },
  fullscreen: {
    type: Boolean,
    default: false
  }
})

const sizeClass = computed(() => {
  const sizes = {
    sm: 'w-4 h-4',
    md: 'w-8 h-8',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16'
  }
  return sizes[props.size]
})

const dotSizeClass = computed(() => {
  const sizes = {
    sm: 'w-1 h-1',
    md: 'w-2 h-2',
    lg: 'w-3 h-3',
    xl: 'w-4 h-4'
  }
  return sizes[props.size]
})

const containerClass = computed(() => {
  return props.fullscreen 
    ? 'fixed inset-0 bg-white bg-opacity-90 z-50' 
    : 'py-8'
})
</script>

<style scoped>
/* Custom border color classes for dynamic colors */
.border-t-primary-600 {
  border-top-color: #2563eb;
}

.border-t-success-600 {
  border-top-color: #16a34a;
}

.border-t-warning-600 {
  border-top-color: #d97706;
}

.border-t-danger-600 {
  border-top-color: #dc2626;
}

.bg-primary-600 {
  background-color: #2563eb;
}

.bg-success-600 {
  background-color: #16a34a;
}

.bg-warning-600 {
  background-color: #d97706;
}

.bg-danger-600 {
  background-color: #dc2626;
}
</style>