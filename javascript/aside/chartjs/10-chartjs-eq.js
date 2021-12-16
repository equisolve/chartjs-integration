class EqChartJsssssss {
    constructor(params) {
        this.id = params.id;
        this.title = params.title;
        this.subtitle = params.subtitle;
        this.type = params.type;
        this.datasets = params.datasets;
        this.overlays = params.overlays;
        this.y1_prefix = params.y1_prefix;
        this.y1_suffix = params.y1_suffix;
        this.y2_prefix = params.y2_prefix;
        this.y2_suffix = params.y2_suffix;
        this.legend_pos = params.legend_pos;
    }
    
    static getData(params) {
        let chart_data = new Promise();
        return chart_data;
    }
}
