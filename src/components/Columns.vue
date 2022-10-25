<template>
  <div class="k-block-type-column">
    <template v-if="columns.length">
      <div class="k-grid">
        <div
          v-for="(col, colIndex) in columns"
          :key="colIndex"
          :data-width="col.width"
          class="k-column"
        >
          <k-blocks-field
            :value="col.blocks"
            v-bind="layouts"
            :label="col.width"
            @input="$emit('update', $event)"
          />
        </div>
      </div>
    </template>

    <template v-else>
      <k-empty icon="dashboard" class="k-layout-empty" @click="selectLayout(0)">
        {{ empty || $t("field.layout.empty") }}
      </k-empty>
    </template>

    <k-dialog
      ref="selector"
      :cancel-button="false"
      :submit-button="false"
      size="small"
      class="k-layout-selector"
    >
      <k-headline>{{ $t("field.layout.select") }}</k-headline>
      <ul>
        <li
          v-for="(layoutOption, layoutOptionIndex) in layouts.layouts"
          :key="layoutOptionIndex"
          class="k-layout-selector-option"
        >
          <k-grid @click.native="assingLayout(layoutOption)">
            <k-column
              v-for="(column, columnIndex) in layoutOption"
              :key="columnIndex"
              :width="column"
            />
          </k-grid>
        </li>
      </ul>
    </k-dialog>
  </div>
</template>

<script>
export default {
  computed: {
    layouts() {
      return this.field("layout");
    },
    columns() {
      return this.content.layout.length ? this.content.layout[0].columns : [];
    },
  },
  watch: {
    columns() {
      if (this.content.layout.length > 1) {
        this.content.layout.splice(1);
      }
    },
  },
  methods: {
    async assingLayout(columns) {
      this.content.layout.push(
        await this.$api.post(this.layouts.endpoints.field + "/layout", {
          columns: columns,
        })
      );
      this.$refs.selector.close();
    },
    selectLayout() {
      this.$refs.selector.open();
    },
  },
};
</script>

<style>
.k-block-type-columns .k-field-header > label {
  padding-bottom: 0;
  font-size: 0.8em;
  color: var(--color-text-light);
}

.k-block-type-columns .k-field-header > .k-dropdown > button > span {
  --size: 1em;
  color: var(--color-text-light);
}
</style>
