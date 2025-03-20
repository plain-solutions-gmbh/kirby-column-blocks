<template>
  <div
    v-if="license !== null"
    class="k-plain-license"
    :style="containerStyle"
    @click="showDialog"
  >
    <k-text :style="textStyle" size="tiny" :html="licenseText" />
    <k-icon :style="iconStyle" type="alert" />
  </div>
</template>

<script>
export default {
  props: {
    prefix: {
      type: String,
      default() {
        return null;
      },
    },
    styling: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  data() {
    return {
      license: null,
    };
  },
  computed: {
    licenseText() {
      if (!this.license) return "";
      return `<strong>${this.license.title}</strong><br />${this.license.cta}`;
    },
    containerStyle() {
      return this.styling && this.styling.container
        ? this.styling.container
        : this.styling;
    },
    textStyle() {
      return this.styling?.text ?? {};
    },
    iconStyle() {
      return this.styling?.icon ?? {};
    },
  },
  created() {
    this.license =
      window.panel.translation.data &&
      window.panel.translation.data["plain.licenses." + this.prefix]
        ? window.panel.translation.data["plain.licenses." + this.prefix]
        : null;

    //Reset license object -> only show once
    window.panel.translation.data["plain.licenses." + this.prefix] = null;
  },
  methods: {
    showDialog() {
      if (this.license && this.license.dialog) {
        this.$dialog(this.license.dialog);
      }
    },
  },
};
</script>

<style>
.k-plain-license {
  cursor: pointer;
  display: flex;
  justify-content: end;
  padding: 7px 0;
  color: var(--color-pink-500);
  pointer-events: none;
}
.k-plain-license > .k-icon {
  width: 25px;
  height: 25px;
  pointer-events: all;
}
.k-plain-license > .k-text {
  padding-right: 7px;
  line-height: 1;
  text-align: right;
  pointer-events: all;
}
</style>
