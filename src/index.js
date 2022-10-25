import Columns from "./components/Columns.vue";
import "./index.css";

window.panel.plugin("microman/column-block", {
  blocks: {
    columns: Columns,
  },
  icons: {
    columns:
      '<path d="M7,1c0-0.6-0.4-1-1-1H1C0.4,0,0,0.4,0,1v14c0,0.6,0.4,1,1,1h5c0.6,0,1-0.4,1-1V1z"/><path d="M9,15c0,0.6,0.4,1,1,1h5c0.6,0,1-0.4,1-1V1c0-0.6-0.4-1-1-1h-5C9.4,0,9,0.4,9,1V15z" fill-opacity="0.25" />',
  },
});
