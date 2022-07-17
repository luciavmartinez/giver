<template>
  <div class="form-group">
    <label :for="label">{{ label }}</label>
    <input :type="type" class="form-control" :id="id" aria-describedby="emailHelp">
    <small :id="['sm' + id]" class="form-text text-muted">{{ error }}</small>
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    type: String,
    id: String,
    error: String
  }
};
</script>
