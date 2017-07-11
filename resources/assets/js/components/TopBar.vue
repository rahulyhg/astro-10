<template>
	<div class="top-bar">
		<div v-show="showBack" @click="backToSites" class="top-bar-backbutton">
			<i class="el-icon-arrow-left backbutton-icon"></i>Back to sites
		</div>
		<!-- TODO: Add richtext toolbar here? -->
		<el-dropdown trigger="click" @command="handleCommand" class="user-menu-button">
			<span class="el-dropdown-link">
				{{ username }}<i class="el-icon-caret-bottom el-icon--right"></i>
			</span>
			<el-dropdown-menu slot="dropdown">
				<el-dropdown-item command="sign-out">Sign out</el-dropdown-item>
			</el-dropdown-menu>
		</el-dropdown>
		<div class="top-title">{{ pageName }}</div>
	</div>
</template>

<script>
import { mapState } from 'vuex';
import { undoStackInstance } from 'plugins/undo-redo';

/* global window */

export default {

	name: 'top-bar',

	computed: {
		...mapState({
			pageName: state => state.page.pageName,
		}),

		showBack() {
			return ['site', 'page'].indexOf(this.$route.name) !== -1;
		},

		username() {
			return window.astro.username;
		}
	},

	methods: {
		handleCommand(command) {
			if(command === 'sign-out') {
				window.location = '/auth/logout';
			}
		},

		backToSites() {
			this.$store.commit('changePage', '');
			this.$store.commit('setPage', {});
			this.$store.commit('setLoaded', false);
			undoStackInstance.clear();
			this.$router.push('/sites');
		}
	}
};
</script>