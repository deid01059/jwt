<template>
	<div>
		<label for="u_id">아이디</label>
		<input type="text" name="u_id" id="u_id" v-model="state.u_id">
		<br>
		<br>
		<label for="u_pw">비밀번호</label>
		<input type="password" name="u_pw" id="u_pw" v-model="state.u_pw">
		<br>
		<br>
		<button @click="login">로그인</button>
	</div>
</template>
<script setup>
import { reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'
const router = useRouter();

// state 단
const state = reactive({
	u_id: '',
	u_pw: '',
});
function login(){
	const URL ='/api/auth/login';
	const formData = new FormData();
	formData.append('u_id',state.u_id)
	formData.append('u_pw',state.u_pw)
	const header = {
		headers: {
			'Content-type': 'multipart/form-data'
		}
	}
	axios.post(URL,formData,header)
	.then(res=>{
		localStorage.setItem('access_token',res.data.access_token);
		router.push('/board');
	})
	.catch(err=>{
		console.log(err.response)
	})
}

</script>
<style>

</style>