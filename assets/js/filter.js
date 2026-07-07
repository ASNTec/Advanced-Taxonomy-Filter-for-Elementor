( function( $ ) {
    'use strict';

    var EMMHierarchicalFilter = {

        init: function() {
            $( document ).on(
                'click',
                '.emm-htf-wrapper .emm-htf-item',
                this.onItemClick.bind( this )
            );
        },

        onItemClick: function( e ) {
            e.preventDefault();
            var $item    = $( e.currentTarget );
            var $wrapper = $item.closest( '.emm-htf-wrapper' );
            var termType = $item.data( 'term-type' );

            if ( termType === 'all' ) {
                this.handleAll( $wrapper );
            } else if ( termType === 'parent' ) {
                this.handleParent( $wrapper, $item );
            } else if ( termType === 'child' ) {
                this.handleChild( $wrapper, $item );
            }
        },

        handleAll: function( $wrapper ) {
            var depth    = String( $wrapper.data( 'depth' ) );
            var taxonomy = $wrapper.data( 'taxonomy' );

            $wrapper.find( '.emm-htf-parent-row .emm-htf-item' ).removeClass( 'emm-htf-active' );
            $wrapper.find( '.emm-htf-parent-row .emm-htf-all' ).addClass( 'emm-htf-active' );

            if ( depth === '2' ) {
                $wrapper.find( '.emm-htf-child-row' ).hide().empty();
                $wrapper.find( '.emm-htf-description' ).hide().empty();
            }

            this.updateURL( taxonomy, '' );
            this.reloadLoopGrid( $wrapper, taxonomy, '' );
        },

        handleParent: function( $wrapper, $item ) {
            var taxonomy        = $wrapper.data( 'taxonomy' );
            var depth           = String( $wrapper.data( 'depth' ) );
            var termId          = String( $item.data( 'term-id' ) );
            var termSlug        = $item.data( 'term-slug' );
            var childrenMap     = $wrapper.data( 'children' ) || {};
            var showDescription = $wrapper.data( 'show-description' );

            $wrapper.find( '.emm-htf-parent-row .emm-htf-item' ).removeClass( 'emm-htf-active' );
            $item.addClass( 'emm-htf-active' );

            if ( depth === '2' ) {
                var children  = childrenMap[ termId ] || [];
                var $childRow = $wrapper.find( '.emm-htf-child-row' );
                var $descBox  = $wrapper.find( '.emm-htf-description' );

                if ( children.length > 0 ) {
                    this.renderChildren( $childRow, children );
                    $childRow.show();

                    var firstChild = children[0];
                    $childRow.find( '.emm-htf-item' ).first().addClass( 'emm-htf-active' );

                    if ( showDescription ) {
                        this.showDescription( $descBox, firstChild.description );
                    }

                    this.updateURL( taxonomy, firstChild.slug );
                    this.reloadLoopGrid( $wrapper, taxonomy, firstChild.slug );
                } else {
                    $childRow.hide().empty();
                    if ( showDescription ) { $descBox.hide().empty(); }
                    this.updateURL( taxonomy, termSlug );
                    this.reloadLoopGrid( $wrapper, taxonomy, termSlug );
                }
            } else {
                this.updateURL( taxonomy, termSlug );
                this.reloadLoopGrid( $wrapper, taxonomy, termSlug );
            }
        },

        handleChild: function( $wrapper, $item ) {
            var taxonomy        = $wrapper.data( 'taxonomy' );
            var termSlug        = $item.data( 'term-slug' );
            var showDescription = $wrapper.data( 'show-description' );
            var description     = $item.data( 'description' ) || '';

            $wrapper.find( '.emm-htf-child-row .emm-htf-item' ).removeClass( 'emm-htf-active' );
            $item.addClass( 'emm-htf-active' );

            if ( showDescription ) {
                this.showDescription( $wrapper.find( '.emm-htf-description' ), description );
            }

            this.updateURL( taxonomy, termSlug );
            this.reloadLoopGrid( $wrapper, taxonomy, termSlug );
        },

        updateURL: function( taxonomy, termSlug ) {
            if ( ! history.pushState ) return;

            var url    = new URL( window.location.href );
            var params = url.searchParams;

            if ( termSlug ) {
                params.set( taxonomy, termSlug );
            } else {
                params.delete( taxonomy );
            }

            history.pushState( null, '', url.toString() );
        },

        getLoopGrid: function( $wrapper ) {
            var queryId = $wrapper.data( 'query-id' );

            if ( queryId ) {
                var $found = $( '.elementor-widget-loop-grid' ).filter( function() {
                    try {
                        var s = $( this ).data( 'settings' );
                        return s && s.query_id === queryId;
                    } catch ( err ) {
                        return false;
                    }
                } );
                if ( $found.length ) {
                    return $found.first();
                }
            }

            return $( '.elementor-widget-loop-grid' ).first();
        },

        reloadLoopGrid: function( $wrapper, taxonomy, termSlug ) {
            var $loopGrid = this.getLoopGrid( $wrapper );

            if ( ! $loopGrid || ! $loopGrid.length ) {
                console.warn( '[EMM HTF] Loop Grid nao encontrado.' );
                return;
            }

            var widgetId = $loopGrid.data( 'id' );
            var postId   = emmHtfConfig.postId;
            var nonce    = emmHtfConfig.nonce;
            var ajaxurl  = emmHtfConfig.ajaxurl;

            var queryVars = {};
            if ( taxonomy && termSlug ) {
                queryVars[ 'tax_query' ] = [ {
                    taxonomy : taxonomy,
                    field    : 'slug',
                    terms    : [ termSlug ]
                } ];
            }

            var data = {
                action     : 'elementor_pro_posts_loop_query',
                post_id    : postId,
                widget_id  : widgetId,
                query_vars : JSON.stringify( queryVars ),
                _nonce     : nonce,
            };

            console.log( '[EMM HTF] Enviando AJAX:', data );

            $loopGrid.css( 'opacity', '0.5' );

            $.ajax( {
                url     : ajaxurl,
                type    : 'POST',
                data    : data,
                success : function( response ) {
                    console.log( '[EMM HTF] Resposta AJAX:', response );

                    if ( response && response.data && response.data.html ) {
                        var $container = $loopGrid.find( '.elementor-loop-container' );
                        if ( ! $container.length ) {
                            $container = $loopGrid.find( '[data-elementor-type]' ).parent();
                        }
                        if ( $container.length ) {
                            $container.html( response.data.html );
                        } else {
                            $loopGrid.html( response.data.html );
                        }
                    }

                    $loopGrid.css( 'opacity', '1' );
                },
                error: function( xhr ) {
                    console.error( '[EMM HTF] Erro AJAX:', xhr.status, xhr.responseText );
                    $loopGrid.css( 'opacity', '1' );
                }
            } );
        },

        renderChildren: function( $childRow, children ) {
            $childRow.empty();
            $.each( children, function( index, child ) {
                var $btn = $( '<button>' )
                    .addClass( 'emm-htf-item emm-htf-child' )
                    .attr( 'data-term-id',     child.id )
                    .attr( 'data-term-slug',   child.slug )
                    .attr( 'data-term-type',   'child' )
                    .attr( 'data-description', child.description || '' )
                    .text( child.name );
                $childRow.append( $btn );
            } );
        },

        showDescription: function( $descBox, text ) {
            if ( text && text.trim() !== '' ) {
                $descBox.html( text ).show();
            } else {
                $descBox.hide().empty();
            }
        }
    };

    $( window ).on( 'elementor/frontend/init', function() {
        EMMHierarchicalFilter.init();
    } );

} )( jQuery );
