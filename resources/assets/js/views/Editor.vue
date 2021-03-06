<template>
<div class="page">

	<div class="editor-body">
		<div class="editor-wrapper" ref="editor">
			<iframe :src="getPreviewUrl" id="editor-content" class="editor-content" :style="dimensions" frameborder="0" />
			<div
				class="iframe-overlay"
				:style="{ 'position' : displayIframeOverlay ? 'absolute' : null }"
			/>
		</div>
		<sidebar />
	</div>

	<modal-container />
</div>
</template>

<script>
import { mapState } from 'vuex';
import { Loading } from 'element-ui';

import Config from 'classes/Config';
import Sidebar from 'components/sidebar';
import ModalContainer from 'components/ModalContainer';
import Icon from 'components/Icon';

export default {
	name: 'editor',

	components: {
		Sidebar,
		ModalContainer,
		Icon
	},

	created() {
        this.$store.commit('site/updateCurrentSiteID', this.$route.params.site_id);
		this.views = {
			desktop: {
				icon: 'desktop',
				label: 'Desktop',
				width: '100%',
				height: '100vh'
			},
			tablet: {
				icon: 'tablet',
				label: 'Tablet',
				width: '768px',
				height: '1024px'
			},
			mobile: {
				icon: 'mobile',
				label: 'Mobile',
				width: '320px',
				height: '568px'
			}
		};
	},

	methods: {

		showLoader() {
			this.loader = Loading.service({
				target: this.$refs.editor,
				text: 'Loading preview...',
				customClass: 'loading-overlay'
			});
		},

		updateCurrentSavedState() {
			this.$store.commit('updateCurrentSavedState');
		}
	},
	computed: {

		...mapState([
			'displayIframeOverlay',
			'currentView'
		]),

		...mapState({
			page: state => state.page.pageData,
			pageLoaded: state => state.page.loaded
		}),

		getPreviewUrl() {
			// TODO: Don't reload page when page_id changes, use state instead
			return `${Config.get('base_url', '')}/preview/${this.$route.params.page_id}`;
		},

		dimensions() {
			return {
				width: this.views[this.currentView].width,
				height: this.views[this.currentView].height
			};
		}

	},

	watch: {
		pageLoaded(hideLoader) {
			this.updateCurrentSavedState();
			if(hideLoader) {
				this.loader.close();
			}
			else {
				this.showLoader();
			}
		}
	},

	mounted() {
		this.showLoader();
	}
};
</script>
