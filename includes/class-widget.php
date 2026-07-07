<?php
namespace EMM_HTF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'emm_hierarchical_taxonomy_filter';
    }

    public function get_title() {
        return __( 'Filtro Hierarquico de Taxonomia', 'emm-htf' );
    }

    public function get_icon() {
        return 'eicon-filter';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_style_depends() {
        return [ 'emm-htf-filter' ];
    }

    public function get_script_depends() {
        return [ 'emm-htf-filter' ];
    }

    protected function register_controls() {

        $this->start_controls_section( 'section_filter', [
            'label' => 'Filtro',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'taxonomy', [
            'label'       => 'Taxonomia',
            'type'        => \Elementor\Controls_Manager::SELECT,
            'options'     => $this->get_taxonomies_options(),
            'default'     => '',
            'description' => 'Selecione a taxonomia que sera usada no filtro.',
        ] );

        $this->add_control( 'query_id', [
            'label'       => 'ID da Consulta do Loop Grid',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'ex: emm360',
            'description' => 'Deve ser o mesmo valor definido no campo ID da Consulta do widget Loop Grid.',
        ] );

        $this->add_control( 'order_meta_key', [
            'label'       => 'Meta Key de Ordem',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'ordem',
            'placeholder' => 'ex: ordem',
            'description' => 'Nome do custom field ACF usado para ordenar os termos da taxonomia.',
        ] );

        $this->add_control( 'depth', [
            'label'   => 'Profundidade',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '1' => 'Apenas pais (profundidade 1)',
                '2' => 'Pais e filhos (profundidade 2)',
            ],
            'default' => '1',
        ] );

        $this->add_control( 'show_all', [
            'label'        => 'Exibir botao Todos',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Sim',
            'label_off'    => 'Nao',
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'all_label', [
            'label'     => 'Texto do botao Todos',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => 'Todos',
            'condition' => [ 'show_all' => 'yes' ],
        ] );

        $this->add_control( 'show_description', [
            'label'        => 'Mostrar descricao da categoria',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Sim',
            'label_off'    => 'Nao',
            'return_value' => 'yes',
            'default'      => '',
            'condition'    => [ 'depth' => '2' ],
            'description'  => 'Exibe a descricao da categoria filha selecionada abaixo dos botoes.',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_parent', [
            'label' => 'Estilo — Categorias Pai',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'parent_justify', [
            'label'     => 'Alinhamento horizontal',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Esquerda',  'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => 'Centro',    'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => 'Direita',   'icon' => 'eicon-h-align-right' ],
                'stretch'    => [ 'title' => 'Expandido', 'icon' => 'eicon-h-align-stretch' ],
            ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row' => 'justify-content: {{VALUE}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'parent_typography',
            'selector' => '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item',
        ] );

        $this->start_controls_tabs( 'parent_tabs' );
        $this->start_controls_tab( 'parent_tab_normal', [ 'label' => 'Normal' ] );
        $this->add_control( 'parent_color', [
            'label'     => 'Cor do texto',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'parent_bg', [
            'label'     => 'Cor de fundo',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'parent_tab_active', [ 'label' => 'Ativo' ] );
        $this->add_control( 'parent_active_color', [
            'label'     => 'Cor do texto ativo',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item.emm-htf-active' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'parent_active_bg', [
            'label'     => 'Cor de fundo ativo',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item.emm-htf-active' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'parent_padding', [
            'label'      => 'Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'parent_border',
            'selector' => '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item',
        ] );

        $this->add_control( 'parent_border_radius', [
            'label'      => 'Border Radius',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-parent-row .emm-htf-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'parent_gap', [
            'label'     => 'Espacamento entre itens',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-parent-row' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_child', [
            'label'     => 'Estilo — Categorias Filha',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'depth' => '2' ],
        ] );

        $this->add_responsive_control( 'child_justify', [
            'label'     => 'Alinhamento horizontal',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Esquerda',  'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => 'Centro',    'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => 'Direita',   'icon' => 'eicon-h-align-right' ],
                'stretch'    => [ 'title' => 'Expandido', 'icon' => 'eicon-h-align-stretch' ],
            ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row' => 'justify-content: {{VALUE}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'child_typography',
            'selector' => '{{WRAPPER}} .emm-htf-child-row .emm-htf-item',
        ] );

        $this->start_controls_tabs( 'child_tabs' );
        $this->start_controls_tab( 'child_tab_normal', [ 'label' => 'Normal' ] );
        $this->add_control( 'child_color', [
            'label'     => 'Cor do texto',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'child_bg', [
            'label'     => 'Cor de fundo',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'child_tab_active', [ 'label' => 'Ativo' ] );
        $this->add_control( 'child_active_color', [
            'label'     => 'Cor do texto ativo',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item.emm-htf-active' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'child_active_bg', [
            'label'     => 'Cor de fundo ativo',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item.emm-htf-active' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'child_padding', [
            'label'      => 'Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'child_border',
            'selector' => '{{WRAPPER}} .emm-htf-child-row .emm-htf-item',
        ] );

        $this->add_control( 'child_border_radius', [
            'label'      => 'Border Radius',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-child-row .emm-htf-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'child_gap', [
            'label'     => 'Espacamento entre itens',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'child_row_margin_top', [
            'label'     => 'Espacamento acima da linha de filhos',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-child-row' => 'margin-top: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_description', [
            'label'     => 'Estilo — Descricao da Categoria',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'depth'            => '2',
                'show_description' => 'yes',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .emm-htf-description',
        ] );

        $this->add_control( 'desc_color', [
            'label'     => 'Cor do texto',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-description' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'desc_bg', [
            'label'     => 'Cor de fundo',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .emm-htf-description' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'desc_padding', [
            'label'      => 'Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'desc_margin_top', [
            'label'     => 'Espacamento acima da descricao',
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
            'selectors' => [ '{{WRAPPER}} .emm-htf-description' => 'margin-top: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'desc_border',
            'selector' => '{{WRAPPER}} .emm-htf-description',
        ] );

        $this->add_control( 'desc_border_radius', [
            'label'      => 'Border Radius',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .emm-htf-description' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();
    }

    private function get_taxonomies_options() {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $options    = [ '' => '-- Selecione --' ];
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
        $settings         = $this->get_settings_for_display();
        $taxonomy         = $settings['taxonomy'];
        $query_id         = trim( $settings['query_id'] );
        $order_key        = trim( $settings['order_meta_key'] ) ?: 'ordem';
        $depth            = $settings['depth'];
        $show_all         = $settings['show_all'] === 'yes';
        $all_label        = $settings['all_label'] ?: 'Todos';
        $show_description = isset( $settings['show_description'] ) && $settings['show_description'] === 'yes';

        if ( empty( $taxonomy ) || empty( $query_id ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding:20px;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;">';
                echo '<strong>EMM Filtro Hierarquico:</strong> Configure a Taxonomia e o ID da Consulta do Loop Grid na aba Conteudo.';
                echo '</div>';
            }
            return;
        }

        $parent_terms = $this->get_parent_terms( $taxonomy, $order_key );

        $children_map = [];
        if ( $depth === '2' ) {
            foreach ( $parent_terms as $parent ) {
                $children = $this->get_child_terms( $taxonomy, $parent->term_id, $order_key );
                $children_map[ strval( $parent->term_id ) ] = array_map( function( $t ) {
                    return [
                        'id'          => $t->term_id,
                        'name'        => $t->name,
                        'slug'        => $t->slug,
                        'description' => $t->description,
                    ];
                }, $children );
            }
        }
        ?>
        <div class="emm-htf-wrapper"
             data-query-id="<?php echo esc_attr( $query_id ); ?>"
             data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
             data-depth="<?php echo esc_attr( $depth ); ?>"
             data-show-description="<?php echo $show_description ? '1' : '0'; ?>"
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
            <?php if ( $show_description ) : ?>
            <div class="emm-htf-description" style="display:none;"></div>
            <?php endif; ?>
            <?php endif; ?>

        </div>
        <?php
    }
}
