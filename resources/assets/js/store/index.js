import Vue from 'vue';
import Vuex from 'vuex';
import undoRedo from '../plugins/undo-redo';
import shareMutations from '../plugins/share-mutations';
import shareTimeTravel from '../plugins/share-time-travel';
import page from './modules/page';
import site from './modules/site';
import media from './modules/media';
import definition from './modules/definition';
import Config from 'classes/Config';


Vue.use(Vuex);

let store = new Vuex.Store({

	/**
	 * The global editor state
	 * @namespace state
	 * @property {Object} over - What coordinates the mouse is over???
	 * @property {number} over.x - X Coordinate
	 * @property {number} over.y - Y Coordinate
	 * @property {Object} WrapperStyles - The styles for the wrapper.
	 */
	state: {
		over: {
			x: 0,
			y: 0
		},
		wrapperStyles: {},
		displayIframeOverlay: false,
		errors: [],
		undoRedo: {
			canUndo: false,
			canRedo: false
		},
		sidebarCollapsed: false,
		blockPicker: {
			visible: false,
			insertIndex: 0,
			insertRegion: 'main'
		},
		currentView: 'desktop',
		publishModal: {
			visible: false
		},
		publishValidationWarningModal: {
			visible: false
		},
		menu: {
			active: 'pages'
		}
	},

	getters: {},

	mutations: {

		changeView(state, currentView) {
			state.currentView = currentView;
		},

		updateOver(state, position) {
			state.over = position;
		},

		updateWrapperStyle(state, { prop, value }) {
			state.wrapperStyles = { ...state.wrapperStyles, [prop]: value };
		},

		showIframeOverlay(state, show = true) {
			state.displayIframeOverlay = show;
		},

		updateErrors(state, errors) {
			state.errors = errors;
		},

		updateUndoRedo(state, canUndoRedo) {
			state.undoRedo = canUndoRedo;
		},

		collapseSidebar(state) {
			state.sidebarCollapsed = true;
		},

		revealSidebar(state) {
			state.sidebarCollapsed = false;
		},

		showBlockPicker(state) {
			state.blockPicker.visible = true;
		},

		hideBlockPicker(state) {
			state.blockPicker.visible = false;
		},

		updateInsertIndex(state, val) {
			state.blockPicker.insertIndex = val;
		},

		updateInsertRegion(state, val) {
			state.blockPicker.insertRegion = val;
		},

		showPublishModal(state) {
			state.publishModal.visible = true;
		},

		hidePublishModal(state) {
			state.publishModal.visible = false;
		},

		showPublishValidationWarningModal(state) {
			state.publishValidationWarningModal.visible = true;
		},

		hidePublishValidationWarningModal(state) {
			state.publishValidationWarningModal.visible = false;
		},

		updateMenuActive(state, id) {
			state.menu.active = id;
		}
	},

	actions: {},

	modules: {
		page,
		definition,
		site,
		media
	},

	plugins: [
		shareMutations,
		undoRedo,
		...(Config.get('debug', false) ? [shareTimeTravel] : [])
	],

	strict: process.env.NODE_ENV !== 'production'

});


export default store;
