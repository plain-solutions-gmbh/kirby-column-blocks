import ColumnsBlock from "@/components/ColumnsBlock.vue";
import "./index.css";

window.panel.plugin("plain/column-block", {
    blocks: {
        columns: ColumnsBlock,
    },
    icons: {
        columns: '<path d="M10.5,1.5C10.5,0.6,9.9,0,9,0H1.5C0.6,0,0,0.6,0,1.5v21C0,23.4,0.6,24,1.5,24H9c0.9,0,1.5-0.6,1.5-1.5V1.5z"/> <path class="st0" d="M13.5,22.5c0,0.9,0.6,1.5,1.5,1.5h7.5c0.9,0,1.5-0.6,1.5-1.5v-21C24,0.6,23.4,0,22.5,0H15 c-0.9,0-1.5,0.6-1.5,1.5V22.5z" fill-opacity="0.25"/>',
    },
});