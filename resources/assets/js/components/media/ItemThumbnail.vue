<template>
<div
	class="image-grid__item"
	:title="item.title"
>
	<lazy-img
		v-if="item.type === 'image'"
		class="img-grid"
		:bg="true"
		:src="(item.variants && item.variants['400x400']) || item.url"
		:smallSrc="item.variants && item.variants.base64 || null"
		:on-load="onLoad"
		:on-start="onStart"
	/>
	<item-icon v-else />
	<div class="image-grid__item-overlay"><slot></slot></div>
	<div v-if="enableOptions" class="item-grid__edit">
		<i class="el-icon-information item-grid__edit-button" @click="onEdit(item)"></i>
		<el-dropdown trigger="click" menu-align="start">
			<i class="el-icon-more"></i>
			<el-dropdown-menu slot="dropdown">
				<el-dropdown-item>
				<a :href="item.url" target="_blank" class="media-item__download-link">Download</a>
    			</el-dropdown-item>
				<el-dropdown-item v-for="option in options" :key="option.text">{{ option.text }}</el-dropdown-item>
			</el-dropdown-menu>
		</el-dropdown>
	</div>
</div>
</template>

<script>
import LazyImg from 'components/LazyImage';
import ItemIcon from './ItemIcon';
import Icon from 'components/Icon';

export default {
	props: {
		item: {
			type: Object,
			required: true
		},
		enableOptions: {
			type: Boolean,
			default: true
		},
		options: {
			default: () => {}
		},
		onEdit: {}
	},

	components: {
		LazyImg,
		ItemIcon,
		Icon
	},

	data() {
		return {
			hideImg: false
		}
	},

	methods: {
		onStart() {
			this.hideImg = false;
		},
		onLoad() {
			this.hideImg = true;
		}
	}
};
</script>