<template>
  <transition
    enter-active-class="transform transition ease-out duration-300"
    enter-from-class="-translate-y-10 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transform transition ease-in duration-500"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="-translate-y-10 opacity-0"
  >
    <div
      v-if="visible"
      class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50"
    >
      <div
        class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-md backdrop-blur-sm border"
        :class="type === 'success'
          ? 'bg-green-50 border-green-200 text-green-700'
          : 'bg-red-50 border-red-200 text-red-700'"
      >
        <div
          class="flex items-center justify-center w-6 h-6 rounded-full"
          :class="type === 'success' ? 'bg-green-100' : 'bg-red-100'"
        >
          <span v-if="type === 'success'">✔</span>
          <span v-else>✕</span>
        </div>
        <p class="text-sm font-medium">{{ message }}</p>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  modelValue: Boolean,
  message: String,
  type: {
    type: String,
    default: "success",
  },
  duration: {
    type: Number,
    default: 3000,
  },
});

const emit = defineEmits(["update:modelValue"]);

const visible = ref(props.modelValue);

watch(
  () => props.modelValue,
  (val) => {
    visible.value = val;
    if (val) {
      setTimeout(() => {
        emit("update:modelValue", false);
      }, props.duration);
    }
  }
);
</script>
