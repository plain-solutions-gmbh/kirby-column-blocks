<template>
  <div class="k-block-type-column">
    <template v-if="columns.length">
      <div class="k-grid">
        <div
          v-for="(col, colIndex) in columns"
          :key="colIndex"
          :style="'--width:' + col.width"
          class="k-column"
        >
          <k-blocks-field
            v-bind="layouts"
            :value="col.blocks"
            :label="col.width"
            @input="onColumnInput(colIndex, $event)"
          />
        </div>
      </div>
    </template>

    <template v-else>
      <k-empty
        icon="dashboard"
        class="k-layout-empty"
        :text="layouts?.empty || $t('field.layout.empty')"
        @click="selectLayout"
      />
    </template>
  </div>
</template>

<script>
export default {
  computed: {
    layouts() {
      return this.field("layout");
    },
    rows() {
      return this.content.layout ?? [];
    },
    columns() {
      return this.rows[0]?.columns ?? [];
    },
  },
  methods: {
    async assignLayout(columns) {
      const row = await this.$panel.api.post(
        this.layouts.endpoints.field + "/layout",
        { columns }
      );

      // the column block only holds a single layout row
      this.update({ layout: [row] });
    },
    onColumnInput(colIndex, blocks) {
      const row = this.rows[0];

      this.update({
        layout: [
          {
            ...row,
            columns: row.columns.map((column, index) =>
              index === colIndex ? { ...column, blocks } : column
            ),
          },
        ],
      });
    },
    selectLayout() {
      this.$panel.dialog.open({
        component: "k-layout-selector",
        props: {
          label: this.$t("field.layout.change"),
          layouts: this.layouts.layouts,
          selector: this.layouts.selector,
          value: null,
        },
        on: {
          submit: (value) => {
            this.assignLayout(value);
            this.$panel.dialog.close();
          },
        },
      });
    },
  },
};
</script>

<style>
.k-block-type-columns > .k-block-type-column > .k-grid {
  gap: var(--spacing-4);
}

.k-block-type-columns .k-field-header > label {
  font-size: 0.8em;
  color: var(--color-text-light);
}

.k-block-type-columns .k-field-header > .k-button-group > button > span {
  --size: 1em;
  color: var(--color-text-light);
}
</style>
