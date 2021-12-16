<?php
// V2 name to prevent collision with the original Chart class for now
class ChartV2 {
    protected $id;
    protected $title;
    protected $subtitle;
    protected $description;
    protected $data;
    protected $labels;
    protected $type;
    protected $source;
        
    public function __construct($record)
    {
        $this->id = h($record->id());
        $this->title = h($record->title());
        $this->subtitle = h($record->subtitle());
        $this->description = $record->description();
        $this->debug = h($record->debug_mode());
        if ($record->chart_type()) {
            $this->type = h($record->chart_type()[0]->value());
        }
        $this->stacked = $record->stacked();
        $this->labels = explode(',', h($record->labels()));
        $label_data = $this->labels;
        array_unshift($label_data, 'Labels');
        $datasets = [];
        $this->sheet_data = [$label_data];
        foreach ($record->datasets() as $dataset) {
            $ds = array(
                'label' => h($dataset->label()),
                'data' => explode(',', h($dataset->csv_data())),
            );
            $sheet_data = $ds['data'];
            array_unshift($sheet_data, $ds['label']);
            $this->sheet_data[] = $sheet_data;
            $colors = $dataset->csv_data_colors();
            if (!$colors) {
                $ds['backgroundColor'] = h($dataset->color());
                $ds['borderColor'] = h($dataset->color());
            } else {
                $color_list = explode(',', h($colors));
                foreach ($color_list as $i => $c) {
                    if (!$c) {
                        $color_list[$i] = h($dataset->color());
                    }
                }
                $ds['backgroundColor'] = $color_list;
                $ds['borderColor'] = $color_list;
            }
            if ($dataset->second_y_axis()) {
                $ds['yAxisID'] = 'y2';
                $this->second_axis = true;
            }
            if ($dataset->type()) {
                $ds['type'] = strtolower(h($dataset->type()[0]->value()));
            }
            $datasets[] = $ds;
        }
        $this->datasets = $datasets;
        $overlays = [];
        foreach ($record->overlay_lines() as $overlay) {
            $overlays[] = array(
                'label' => $overlay->label(),
                'color' => $overlay->color(),
                'x1' => $overlay->x_start(),
                'y1' => $overlay->y_start(),
                'x2' => $overlay->x_end(),
                'y2' => $overlay->y_end(),
            );
        }
        $this->overlays = $overlays;
        $this->y1_prefix = h($record->y_axis_prefix());
        $this->y1_suffix = h($record->y_axis_suffix());
        $this->y2_prefix = h($record->second_y_axis_prefix());
        $this->y2_suffix = h($record->second_y_axis_suffix());
        if ($record->legend_position()) {
            if ($record->legend_position()[0]->value() == 'Chart Area') {
                $this->legend_pos = 'chartArea';
            } else {
                $this->legend_pos = strtolower(h($record->legend_position()[0]->value()));
            }
        }
        if ($record->legend_alignment()) {
            $this->legend_alignment = h($record->legend_alignment()[0]->value());
        }
    }
    
    // Prepares data for usage in Chart.js
    public function prep_data()
    {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'type' => strtolower($this->type),
            'stacked' => $this->stacked,
            'labels' => $this->labels,
            'datasets' => $this->datasets,
            'sheet_data' => $this->sheet_data,
            'second_axis' => $this->second_axis,
            'overlays' => $this->overlays,
            'y1_prefix' => $this->y1_prefix,
            'y1_suffix' => $this->y1_suffix,
            'y2_prefix' => $this->y2_prefix,
            'y2_suffix' => $this->y2_suffix,
            'legend_pos' => $this->legend_pos,
            'legend_alignment' => strtolower($this->legend_alignment),
        );
    }
    
    public function id()
    {
        return $this->id;
    }
    
    public function title()
    {
        return $this->title;
    }
    
    public function subtitle()
    {
        return $this->subtitle;
    }
    
    public function description()
    {
        return $this->description;
    }
    
    public function debug()
    {
        return $this->debug;
    }
}
?>