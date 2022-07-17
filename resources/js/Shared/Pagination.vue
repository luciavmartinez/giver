<template>
  <div class="flex flex-row sm:flex-row items-center justify-center flex-wrap">
    <component
      preserve-state
      preserve-scroll
      :is="link.url ? 'Link' : 'span'"
      v-for="link in links"
      :href="link.url"
      v-html="link.label"
      style="min-width: 35px"
      class="px-1 no-underline h-10 sm:my-0 flex justify-center items-center border border-gray-300 rounded-sm transition-colors duration-200 ease hover:border-gray-400 hover:text-blue-400"
      :class="{ 'text-gray-500': ! link.url, 'font-bold text-sky-600' : link.active }"
    />
  </div>
</template>

<script>
export default {
  props: {
    links: Array
  }
};
</script>
