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
      this.$emit("update");
    },
    selectLayout() {
      this.$panel.dialog.open({
        component: "k-layout-selector",
        props: {
          label: this.$t("field.layout.change"),
          layouts: this.layouts.layouts,
          selector: this.selector,
        },
        on: {
          submit: (value) => {
            this.assingLayout(value);
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
