<template>
<el-dialog
	class="tabbed-dialog"
	title="Add block(s)"
	size="large"
	top="0%"
	v-model="visible"
>
	<el-tabs type="border-card">
		<el-tab-pane label="All blocks">
			<p>Select one or more blocks from below that you'd like to add to the page.</p>
			<div class="el-dialog__footer">
				<el-button @click="cancel">Cancel</el-button>
				<el-button type="primary" @click="addBlocks">Add selected blocks to the page</el-button>
			</div>
			<block-list :selectedBlocks="selected"/>
		</el-tab-pane>
	</el-tabs>

	<span slot="footer" class="dialog-footer">
		<el-button @click="cancel">Cancel</el-button>
		<el-button type="primary" @click="addBlocks">Add selected blocks to the page</el-button>
	</span>
</el-dialog>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import Vue from 'vue';
import { uuid } from 'classes/helpers';
import BlockList from 'components/BlockList';

export default {
	name: 'block-picker',

	components: {
		BlockList
	},

	data() {
		return {
			selected: []
		};
	},

	computed: {
		...mapState([
			'blockPicker'
		]),

		...mapState({
			blockDefinitions: state => state.definition.blockDefinitions
		}),

		visible: {
			get() {
				return this.blockPicker.visible;
			},
			set(value) {
				if(value) {
					this.showBlockPicker();
				}
				else {
					this.hideBlockPicker();
				}
			}
		}
	},

	methods: {
		...mapMutations([
			'showBlockPicker',
			'hideBlockPicker',
			'addBlock'
		]),

		addBlocks() {

			this.selected.forEach((blockKey, i) => {
				const { name, version } = this.blockDefinitions[blockKey];
				this.addThisBlockType({
					name,
					version,
					index: this.blockPicker.insertIndex + i
				});
			});

			if(this.selected.length) {
				this.hideBlockPicker();
				this.selected = [];
				// wait for next tick to sync mutation
				// then another tick for position update
				Vue.nextTick(() =>
					Vue.nextTick(() => this.$bus.$emit('block:updateOverlay'))
				);
			}
		},

		addThisBlockType({ name, version = 1, index }) {

			const block = {
				definition_name: name,
				definition_version: version,
				id: uuid(),
				fields: {}
			};

			this.addBlock({
				index,
				block,
				region: this.blockPicker.insertRegion
			});
		},

		cancel() {
			this.hideBlockPicker();
			this.selected = [];
		}
	}
};
</script>
