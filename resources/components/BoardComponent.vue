<template>
	<h1>보드페이지 입니다.</h1>
	<button @click="logout">로그아웃</button>
	<div>
		보드보드보드
	</div>
</template>
<script setup>
import { reactive,onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'
import router from '../js/router';

function getBoardInfo(){
	const URL ='/api/boards';
	const header = {
		headers: {
			Authorization: 'Bearer '+localStorage.getItem('access_token')
		}
	}
	axios.get(URL,header)
	.then(res=>{
		console.log(res.data.msg);
	})
	.catch(err=>{
		console.log(err.response);
		// 서버에 엑세스토큰 재발급 처리
		router.push('/login');
	});
}

function logout(){
	localStorage.clear();
	router.push('/login');
}

onMounted(()=>{
	if(!(localStorage.getItem('access_token'))){
		router.push('/login')
	} else {
		getBoardInfo();
	}
});
</script>