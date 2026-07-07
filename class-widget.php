<?php
namespace EMM_HTF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'emm_hierarchical_taxonomy_filter';
    }

    public function get_title() {
        return __( 'Filtro HierÃ¡rquico de Taxonomia', 'emm-htf' );
    }

    public function get_icon() {
        return 'eicon-filter';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_style_depends() {
        return [ 'emm-htf-style' ];
    }

    public function get_script_depends() {
        return [ 'emm-htf-script' ];
    }

    protected function register_controls() {

        $this->start_controls_section( 'section_filter', [
            'label' => __( 'Filtro', 'emm-htf' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'taxonomy', [
            'label'       => __( 'Taxonomia', 'emm-htf' ),
            'type'        => \Elementor\Controls_Manager::SELECT,
            'options'     => $this->get_taxonomies_options(),
            'default'     => '',
            'description' => __( 'Selecione a taxonomia que serÃ¡ usada no filtro.', 'emm-htf' ),
        ] );

        $this->add_control( 'query_id', [
            'label'       => __( 'ID da Consulta do Loop Grid', 'emm-htf' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'ex: emm360',
            'description' => __( 'Deve ser o mesmo valor definido no campo "ID da Consulta" do widget Loop Grid.', 'emm-htf' ),
        ] );

        $this->add_control( 'order_meta_key', [
            'label'       => __( 'Meta Key de Ordem', 'emm-htf' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'ordem',
            'placeholder' => 'ex: ordem',
            'description' => __( 'Nome do custom field ACF usado para ordenar os termos da taxonomia.', 'emm-htf' ),
        ] );

        $this->add_control( 'depth', [
            'label'   => __( 'Profundidade', 'emm-htf' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '1' => __( 'Apenas pais (profundidade 1)', 'emm-htf' ),
                '2' => __( 'Pais e filhos (profundidade 2)', 'emm-htf' ),
            ],
            'default' => '1',
        ] );

        $this->add_control( 'show_all', [
            'label'        => __( 'Exibir botÃ£o "Todos"', 'emm-htf' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => __( 'Sim', 'emm-htf' ),
            'label_off'    => __( 'NÃ£o', 'emm-htf' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'all_label', [
            'label'     => __( 'Texto do botÃ£o "Todos"', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => __( 'Todos', 'emm-htf' ),
            'condition' => [ 'show_all' => 'yes' ],
        ] );

        $this->end_controls_section();

        // --- ESTILO PAIS ---

        $this->start_controls_section( 'section_style_parent', [
            'label' => __( 'Estilo â€” Categorias Pai', 'emm-htf' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'parent_typography',
            'selector' => '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item',
        ] );

        $this->start_controls_tabs( 'parent_tabs' );

        $this->start_controls_tab( 'parent_tab_normal', [ 'label' => __( 'Normal', 'emm-htf' ) ] );
        $this->add_control( 'parent_color', [
            'label'     => __( 'Cor do texto', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'parent_bg', [
            'label'     => __( 'Cor de fundo', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'parent_tab_active', [ 'label' => __( 'Ativo', 'emm-htf' ) ] );
        $this->add_control( 'parent_active_color', [
            'label'     => __( 'Cor do texto ativo', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item.emm-htf-active' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'parent_active_bg', [
            'label'     => __( 'Cor de fundo ativo', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item.emm-htf-active' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'parent_padding', [
            'label'      => __( 'Padding', 'emm-htf' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'parent_border',
            'selector' => '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item',
        ] );

        $this->add_control( 'parent_border_radius', [
            'label'      => __( 'Border Radius', 'emm-htf' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'parent_gap', [
            'label'     => __( 'EspaÃ§amento entre itens', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        // --- ESTILO FILHOS ---

        $this->start_controls_section( 'section_style_child', [
            'label'     => __( 'Estilo â€” Categorias Filha', 'emm-htf' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'depth' => '2' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'child_typography',
            'selector' => '{{WRAPPER}} .emm-htf-child-row .emm-htf-item',
        ] );

        $this->start_controls_tabs( 'child_tabs' );

        $this->start_controls_tab( 'child_tab_normal', [ 'label' => __( 'Normal', 'emm-htf' ) ] );
        $this->add_control( 'child_color', [
            'label'     => __( 'Cor do texto', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'child_bg', [
            'label'     => __( 'Cor de fundo', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'child_tab_active', [ 'label' => __( 'Ativo', 'emm-htf' ) ] );
        $this->add_control( 'child_active_color', [
            'label'     => __( 'Cor do texto ativo', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item.emm-htf-active' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'child_active_bg', [
            'label'     => __( 'Cor de fundo ativo', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item.emm-htf-active' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'child_padding', [
            'label'      => __( 'Padding', 'emm-htf' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'child_border',
            'selector' => '{{WRAPPER}} .emm-htf-child-row .emm-htf-item',
        ] );

        $this->add_control( 'child_border_radius', [
            'label'      => __( 'Border Radius', 'emm-htf' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'child_gap', [
            'label'     => __( 'EspaÃ§amento entre itens', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'child_row_margin_top', [
            'label'     => __( 'EspaÃ§amento acima da linha de filhos', 'emm-htf' ),
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row' => 'margin-top: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();
    }

    private function get_taxonomies_options() {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $options    = [ '' => __( 'â€” Selecione â€”', 'emm-htf' ) ];
        foreach ( $taxonomies as $taxonomy ) {
            $options[ $taxonomy->name ] = $taxonomy->label;
        }
        return $options;
    }

    private function get_parent_terms( $taxonomy, $order_meta_key ) {
        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'parent'     => 0,
            'meta_key'   => $order_meta_key,
            'orderby'    => 'meta_value_num',
            'order'      => 'ASC',
        ] );
        return is_wp_error( $terms ) ? [] : $terms;
    }

    private function get_child_terms( $taxonomy, $parent_id, $order_meta_key ) {
        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'parent'     => $parent_id,
            'meta_key'   => $order_meta_key,
            'orderby'    => 'meta_value_num',
            'order'      => 'ASC',
        ] );
        return is_wp_error( $terms ) ? [] : $terms;
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $taxonomy  = $settings['taxonomy'];
        $query_id  = trim( $settings['query_id'] );
        $order_key = trim( $settings['order_meta_key'] ) ?: 'ordem';
        $depth     = $settings['depth'];
        $show_all  = $settings['show_all'] === 'yes';
        $all_label = $settings['all_label'] ?: __( 'Todos', 'emm-htf' );

        if ( empty( $taxonomy ) || empty( $query_id ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding:20px;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;">';
                echo '<strong>' . esc_html__( 'EMM Filtro HierÃ¡rquico:', 'emm-htf' ) . '</strong> ';
                echo esc_html__( 'Configure a Taxonomia e o ID da Consulta do Loop Grid na aba ConteÃºdo.', 'emm-htf' );
                echo '</div>';
            }
            return;
        }

        $parent_terms = $this->get_parent_terms( $taxonomy, $order_key );

        $children_map = [];
        if ( $depth === '2' ) {
            foreach ( $parent_terms as $parent ) {
                $children = $this->get_child_terms( $taxonomy, $parent->term_id, $order_key );
                $children_map[ $parent->term_id ] = array_map( function( $t ) {
                    return [ 'id' => $t->term_id, 'name' => $t->name, 'slug' => $t->slug ];
                }, $children );
            }
        }

        $widget_id = $this->get_id();
        ?>
        <div class="emm-htf-wrapper"
             data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
             data-query-id="<?php echo esc_attr( $query_id ); ?>"
             data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
             data-depth="<?php echo esc_attr( $depth ); ?>"
             data-children='<?php echo wp_json_encode( $children_map ); ?>'>

            <div class="emm-htf-parent-row">
                <?php if ( $show_all ) : ?>
                    <button class="emm-htf-item emm-htf-all emm-htf-active"
                            data-term-id=""
                            data-term-slug=""
                            data-term-type="all">
                        <?php echo esc_html( $all_label ); ?>
                    </button>
                <?php endif; ?>
                <?php foreach ( $parent_terms as $term ) : ?>
                    <button class="emm-htf-item emm-htf-parent"
                            data-term-id="<?php echo esc_attr( $term->term_id ); ?>"
                            data-term-slug="<?php echo esc_attr( $term->slug ); ?>"
                            data-term-type="parent">
                        <?php echo esc_html( $term->name ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if ( $depth === '2' ) : ?>
            <div class="emm-htf-child-row" style="display:none;"></div>
            <?php endif; ?>

        </div>
        <?php
    }
}
