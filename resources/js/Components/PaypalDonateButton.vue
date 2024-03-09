<script setup>
import { onMounted } from 'vue';

function loadPaypalButton()
{
	PayPal.Donation.Button({
		env: 'production',
		hosted_button_id: 'L3DCXJMNXM3PS',
		image: {
			src: 'https://www.paypalobjects.com/it_IT/IT/i/btn/btn_donateCC_LG.gif',
			alt: 'Fai una donazione con il pulsante PayPal',
			title: 'PayPal - The safer, easier way to pay online!',
		}
	}).render('#donate-button');
}

onMounted(() => {
	if (typeof PayPal === 'undefined') {
		const script = document.createElement('script');
		script.src = 'https://www.paypalobjects.com/donate/sdk/donate-sdk.js';
		script.charset = 'UTF-8';
		document.head.appendChild(script);
		script.onload = loadPaypalButton;
		return
	}
	loadPaypalButton();
});
</script>

<template>
	<div id="donate-button-container">
		<div id="donate-button"></div>
	</div>
</template>
