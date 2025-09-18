<template>
    <a-row v-if="!datarecords">
        Gettng data please wait...
    </a-row>
    <a-row v-else class="flex justify-between mt-5" align="middle" v-if="datarecords !== undefined">
        <a-col>
            <a-typography-text>{{ `Showing ${datarecords?.from || 0} to ${datarecords?.to || 0} of ${datarecords?.total.toLocaleString()}
                records` }}
            </a-typography-text>
        </a-col>
        <a-col>
            <a-config-provider>
                <template v-for="(link, key) in datarecords?.links" :key="`link-${key}`">
                    <!-- <a-button class="px-3 py-1 border rounded-md text-sm font-medium transition-colors"  style="border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 2px 0px;" :type="link.active ? 'primary' : 'default'"
                        v-html="link.label" @click="$emit('onPagination',link)" /> -->
                        <button style="box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 2px 0px;"
             @click="$emit('onPagination',link)"
            v-html="link.label"
            class="px-3 py-1 border rounded-md text-sm font-medium transition-colors"
            :class="{
              ' bg-blue-600 text-white border-blue-500': link.active,
              'bg-white border-gray-300 hover:bg-gray-200': !link.active
            }"
          />
                </template>
            </a-config-provider>
        </a-col>
    </a-row>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import axios from 'axios';

export default {
    components: {
        Link,
    },
    props: {
        datarecords: Object,
    },
}
</script>
