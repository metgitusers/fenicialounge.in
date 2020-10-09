( function( editor, components, i18n, element, $ ) {
	"use strict";

	const __ = i18n.__;
	const el = element.createElement;
	const compose = wp.compose.compose;
	const registerPlugin = wp.plugins.registerPlugin;

	const {
		Fragment,
		Component 
	} = element;


	const {
		TextareaControl,
		PanelBody
	} = components;

	const {
		dispatch,
		withSelect,
		withDispatch 
	} = wp.data;

	const {
		PluginSidebar,
		PluginSidebarMoreMenuItem 
	} = wp.editPost;

	const Icon = el( 'svg', {
			height: '20px',
			width: '20px',
			viewBox: '0 0 17.39 17.39'
		}, el ( 'polygon', {
			points: '14.77 11.19 17.3 8.65 14.77 6.12 14.77 2.53 11.19 2.53 8.65 0 6.12 2.53 2.53 2.53 2.53 6.12 0 8.65 2.53 11.19 2.53 14.77 6.12 14.77 8.65 17.3 11.19 14.77 14.77 14.77 14.77 11.19'
		} )
	);

	class LoftLoaderPlugin extends Component {
		constructor() {
			super( ...arguments );

			this.state = this.props.meta;
		}
		onSaveMeta( newValue ) {
			dispatch( 'core/editor' ).editPost( { meta: { 'loftload-saving': 1 } } );
			this.setState( newValue );
		}
		static getDerivedStateFromProps( nextProps, state ) {
			if ( ( nextProps.isPublishing || nextProps.isSaving ) && ! nextProps.isAutoSaving ) {
				wp.apiRequest( { path: '/loftloader/v2/update-meta?id=' + nextProps.postId, method: 'POST', data: state } ).then(
					( data ) => {
						return data;
					}, ( err ) => {
						return err;
					}
				);
			}
		}
		render() {
			return el( Fragment, {},
				el( PluginSidebarMoreMenuItem, { target: 'loftloader-any-page' }, __( 'LoftLoader Any Page Shortcode' ) ),
				el( PluginSidebar, { name: 'loftloader-any-page', title: __( 'LoftLoader Any Page Shortcode' ) },
					el( PanelBody, { 
							className: 'loftloader-any-page-sidebar',
							initialOpen: true
						},
						el( TextareaControl, {
							label: __( 'Paste LoftLoader shortcode into the box below' ),
							value: this.state.loftloader_page_shortcode,
							onChange: ( value ) => {
								this.onSaveMeta( { loftloader_page_shortcode: value } );
							}
						} )
					),
					el( 'input', {
						type: 'hidden',
						name: 'loftloader_gutenberg_enabled', 
						value: 'on'
					} )
				)
			);
		}
	}

	// Fetch the post meta.
	const applyWithSelect = withSelect( ( select, { forceIsSaving } ) => { 
		const {
			getEditedPostAttribute,
			getCurrentPostId,
			isSavingPost,
			isPublishingPost,
			isAutosavingPost,
		} = select( 'core/editor' );

		return {
			meta: getEditedPostAttribute( 'meta' ),
			postId: getCurrentPostId(),
			isSaving: forceIsSaving || isSavingPost(),
			isAutoSaving: isAutosavingPost(),
			isPublishing: isPublishingPost(),
		};
	} );

	const render = compose( [
		applyWithSelect
	] )( LoftLoaderPlugin );

	registerPlugin( 'loftloader-any-page', {
		icon: Icon,
		render
	} );
} )(
	window.wp.editor,
	window.wp.components,
	window.wp.i18n,
	window.wp.element,
	jQuery
);