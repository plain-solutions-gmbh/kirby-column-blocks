(function() {
  "use strict";
  var render = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "k-block-type-column" }, [_vm.columns.length ? [_c("div", { staticClass: "k-grid" }, _vm._l(_vm.columns, function(col, colIndex) {
      return _c("div", { key: colIndex, staticClass: "k-column", style: "--width:" + col.width }, [_c("k-blocks-field", _vm._b({ attrs: { "value": col.blocks, "label": col.width }, on: { "input": function($event) {
        return _vm.$emit("update", $event);
      } } }, "k-blocks-field", _vm.layouts, false))], 1);
    }), 0)] : [_c("k-empty", { staticClass: "k-layout-empty", attrs: { "icon": "dashboard" }, on: { "click": function($event) {
      return _vm.selectLayout(0);
    } } }, [_vm._v(" " + _vm._s(_vm.empty || _vm.$t("field.layout.empty")) + " ")])]], 2);
  };
  var staticRenderFns = [];
  render._withStripped = true;
  var ColumnsBlock_vue_vue_type_style_index_0_lang = "";
  function normalizeComponent(scriptExports, render2, staticRenderFns2, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render2) {
      options.render = render2;
      options.staticRenderFns = staticRenderFns2;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        );
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const __vue2_script = {
    computed: {
      layouts() {
        return this.field("layout");
      },
      columns() {
        return this.content.layout.length ? this.content.layout[0].columns : [];
      }
    },
    watch: {
      columns() {
        if (this.content.layout.length > 1) {
          this.content.layout.splice(1);
        }
      }
    },
    methods: {
      async assingLayout(columns) {
        this.content.layout.push(
          await this.$api.post(this.layouts.endpoints.field + "/layout", {
            columns
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
            selector: this.selector
          },
          on: {
            submit: (value) => {
              this.assingLayout(value);
              this.$panel.dialog.close();
            }
          }
        });
      }
    }
  };
  const __cssModules = {};
  var __component__ = /* @__PURE__ */ normalizeComponent(
    __vue2_script,
    render,
    staticRenderFns,
    false,
    __vue2_injectStyles,
    null,
    null,
    null
  );
  function __vue2_injectStyles(context) {
    for (let o in __cssModules) {
      this[o] = __cssModules[o];
    }
  }
  __component__.options.__file = "src/components/ColumnsBlock.vue";
  var ColumnsBlock = /* @__PURE__ */ function() {
    return __component__.exports;
  }();
  var index = "";
  window.panel.plugin("microman/column-block", {
    blocks: {
      columns: ColumnsBlock
    },
    icons: {
      columns: '<path d="M10.5,1.5C10.5,0.6,9.9,0,9,0H1.5C0.6,0,0,0.6,0,1.5v21C0,23.4,0.6,24,1.5,24H9c0.9,0,1.5-0.6,1.5-1.5V1.5z"/> <path class="st0" d="M13.5,22.5c0,0.9,0.6,1.5,1.5,1.5h7.5c0.9,0,1.5-0.6,1.5-1.5v-21C24,0.6,23.4,0,22.5,0H15 c-0.9,0-1.5,0.6-1.5,1.5V22.5z" fill-opacity="0.25"/>'
    }
  });
})();
