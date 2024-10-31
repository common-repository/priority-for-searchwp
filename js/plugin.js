/**
 * Internal block libraries
 */
( function( blocks, i18n, element) {
	var __ = i18n.__;

	var { Fragment } = wp.element;
	var el = wp.element.createElement;
	
	var {
		PluginSidebar,
		PluginSidebarMoreMenuItem,
		PluginPostStatusInfo,
		Panel,
		PanelBody,
		PanelRow,
		InspectorControls,
		BlockControls,
		TextControl,
		TextareaControl
	} = wp.editPost;
	
	var { registerPlugin } = wp.plugins;
	var {getCurrentPostId} = wp.data.select("core/editor");
	
	var editPostStatus = wp.data.dispatch( 'core/editor' ).editPost;
    
	var postID = getCurrentPostId();
	var state = {id: postID, value: '' };
	var once = false;
	
	const customIcon = el('svg', 
			{ 
				width: 20, 
				height: 20,
				viewBox: "0 0 42 42",
				class: "dashicon dashicons-admin-generic",
				xmlns: "http://www.w3.org/2000/svg"
			},
			el( 'path',
				{ 
					d: "M34.252,30.717l-0.707,0.707l-1.791-1.791c0.789-0.932,1.472-1.953,2.061-3.031c0.01-0.018,0.019-0.035,0.028-0.053   c0.268-0.494,0.512-1.002,0.733-1.523c0.006-0.012,0.011-0.023,0.016-0.036c0.908-2.148,1.427-4.5,1.427-6.979   C36.019,8.064,27.955,0,18.009,0c-0.001,0-0.003,0-0.004,0C18.003,0,18.001,0,18,0C8.059,0,0,8.06,0,18c0,0.001,0,0.003,0,0.005   c0,0.001,0,0.003,0,0.004c0,9.945,8.063,18.01,18.009,18.01c2.479,0,4.832-0.52,6.979-1.428c0.012-0.004,0.024-0.01,0.037-0.016   c0.521-0.221,1.028-0.465,1.522-0.732c0.018-0.01,0.036-0.02,0.054-0.029c1.079-0.588,2.1-1.271,3.031-2.061l1.791,1.791   l-0.707,0.709L44.463,48L48,44.465L34.252,30.717z M18,32.418c-7.732,0-14-6.268-14-14c0-7.732,6.268-14,14-14   c7.732,0,14,6.268,14,14C32,26.15,25.732,32.418,18,32.418z"
				}
			),
			el( 'path',
				{ 
					d: "M27.387,14.256l-5.455,5.457l-4.198-0.638c0,0-0.631-4.252-0.649-4.234l5.444-5.442c-1.225-0.554-2.577-0.87-4.008-0.87   c-5.375,0-9.734,4.358-9.734,9.735c0,1.43,0.316,2.784,0.869,4.006L8,23.926c0.748,1.643,2.187,3.633,4.901,4.82l1.615-1.616   c1.221,0.551,2.577,0.869,4.004,0.869c5.377,0,9.735-4.358,9.735-9.735C28.256,16.833,27.939,15.482,27.387,14.256z"
				}
			)
		);
	
	function Component() {
		return el(
	        Fragment,
	        {},
	        el(PluginSidebarMoreMenuItem,
	        	{target: 'priority-for-searchwp-sidebar'},
	            __('Priority for SearchWP', 'priority-for-searchwp')
	        ),
	        el(PluginSidebar,
	            {
	                name: 'priority-for-searchwp-sidebar',
	                title: __('Priority for SearchWP', 'priority-for-searchwp'),
	            },
	            //'Content of the sidebar'
	            el('div', {
	            		'style': {'padding': '10px'}
	            	},
	            	el('label', 
			        	{'for': 'search_wp_keywords'}, 
			        	__('Search keywords CSV', 'priority-for-searchwp')
			        	
			        ),
			        el('br'),
			        el('textarea', 
					{
						'name': 'search_wp_keywords',
						'id': 'search_wp_keywords',
						'placeholder': __('Ex: apple, orange color, fruits and veggies', 'priority-for-searchwp'),
						'style': {'width': '100%'},
						'onChange': (evt)=>{
							wp.data.dispatch("core/editor").editPost(
								{ meta: 
									{ 
										'search_wp_keywords': evt.target.value
									} 
								});
							editPostStatus({edited: true});
						},
						defaultValue: `${wp.data.select("core/editor").getEditedPostAttribute("meta").search_wp_keywords}`
					}),
					
	            )
	        )
	    );
	}
	
	// Register plugin
	registerPlugin( 'priority-for-search', {
	    icon: customIcon,
	    render: Component,
	} );
	
}(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element
) );
