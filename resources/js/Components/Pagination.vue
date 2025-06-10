<template>
  <div class="flex justify-between items-center" v-if="datarecords !== undefined">
    <div>
      <span class="text-gray-700">
        Showing {{ datarecords?.from || 0 }} to {{ datarecords?.to || 0 }} of
        {{ datarecords?.total.toLocaleString() }} records
      </span>
    </div>
    <div>
      <div class="inline-flex gap-[1px]">
        <template v-for="(link, key) in datarecords.links" :key="`link-${key}`">
          <button style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;"
            @click="paginate(link)"
            v-html="link.label"
            class="px-3 py-1 border rounded-md text-sm font-medium transition-colors"
            :class="{
              ' bg-blue-600 text-white border-blue-500': link.active,
              'bg-white border-gray-300 hover:bg-gray-200': !link.active
            }"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link,
  },
  props: {
    datarecords: Object,
  },
  methods: {
    paginate(link) {
      if (link.url) {
        this.$inertia.visit(link.url, {
          preserveState: true,
          preserveScroll: true
        })
      }
    }
  }
}
</script>
