<script setup lang="ts">
import type { Immobilie } from '~/types/domain';
import type { Params } from '~/types/nav';

const { t } = useI18n();
const props = defineProps<{
  loading: boolean,
  immobilien: Immobilie[],
  pagination: {current: number, last: number}
}>();

const immobilienStore = useImmobilienStore();

const viewAs = ref<'GRID'|'LIST'>('GRID');
const emits = defineEmits(['update']);

const query = ref<object>({});

function apply(queryParams:object) {
  query.value = queryParams;
  emits('update', query.value);
}

function reset() {
  query.value = {};
  viewAs.value = 'GRID';
  emits('update', query.value);
}

function changePage(page:number) {
  query.value = {...query.value, page: page};
  emits('update', query.value);
}
</script>

<template>
<div class="immo-browser">
  <div class="top">
    <Toolbar 
    :hide-usage-filter="false" 
    :hide-type-filter="false" 
    @change-view="(view:'GRID'|'LIST')=>{ viewAs = view}"
    @apply="apply"
    @reset="reset"
    class="toolbar"/>
  </div>
  <div class="loading" v-if="loading">
      <LoadingIcon/>
  </div>
  <div class="no-results" v-if="!loading && (immobilien.length === 0)">
    <div>
      <NoImmobilienIcon/>
      <p>{{ t('no-results') }}</p>
    </div>
  </div>
  <div class="middle" v-if="!loading && (immobilien.length > 0)">
     <ImmoGrid v-if="viewAs === 'GRID'"></ImmoGrid>
     <ImmoTable v-if="viewAs === 'LIST'"></ImmoTable>
  </div>
  <div class="bottom">
    <Paginator :current="pagination.current"
    :pages="pagination.last"
    @change-page="changePage"></Paginator>
  </div>
</div>
</template>


<style lang="css" scoped>
.no-results {
  display: inline flex;
  flex-flow: row;
  align-items: center;
  justify-content: center;
  height: 500px;
}

.no-results>div {
  display: flex;
  flex-flow: column nowrap;
  gap: 1rem;
  justify-content: center;
  align-items: center;
  font-size: calc(var(--font-size) * 1.3);
  text-align: center;
  max-width: 500px;
  font-weight: 600;
}
.no-results>div>:deep(svg) {
  width: 200px;
  color: var(--pink);
}

.loading {
  height: 500px;
  display: flex;
  flex-flow: column;
  align-items: center;
  justify-content: center;
}

.loading>:deep(svg) {
  color: var(--pink);
  width: 150px;
}

.bottom {
  align-self: center;
}

.immo-browser {
  display: flex;
  flex-flow: column nowrap;
  align-items: stretch;
  padding: 10px 5px;
  margin: 10px;
  gap: 30px;
  position: relative;
}

.transparent {
  height: 15vh;
}

.toolbar {
  /* position: sticky; */
  transform: translateY(-60px);
  z-index: 50;
}

.middle {
    padding: 0;
}

</style>

<i18n lang="json">
{
  "en": {
    "no-results": "There are currently no real-estate available for viewing"
  },
  "de": {
    "no-results": "Zur Zeit sind keine Immobilien verfügbar, die Sie besichtigen können"
  }
}
</i18n>
