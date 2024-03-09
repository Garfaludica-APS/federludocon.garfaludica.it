<script setup>
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { trans } from 'laravel-vue-i18n';

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import FooterNavLink from '@/Components/FooterNavLink.vue';
import PayPalDonateButton from '@/Components/PaypalDonateButton.vue';

const phoneNumber = '+39 XXX XXX XXXX'; // TODO: move to props
const emailAddress = 'info@garfaludica.it';
const webAddress = 'www.garfaludica.it';

function showToast(msg, timeout = 3000)
{
	const t = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
	toast(msg, { autoClose: timeout, theme: t, position: 'bottom-center' });
}

function copyToClipboard(text)
{
	navigator.clipboard.writeText(text).then(() => {
		showToast(trans('Copied to clipboard'));
	}, () => {
		showToast(trans('Error while copying to clipboard'));
	});
}
</script>

<template>
	<div class="container flex min-w-full flex-wrap text-sm text-black dark:text-white">
		<div class="basis-full sm:basis-1/2 lg:basis-2/5 px-3 pb-6">
			<ApplicationLogo class="block h-12 w-auto" />
			<p class="mt-4 [&>a]:text-indigo-500" v-html="$t('footer_org_text')"></p>
			<p>{{ $t('Tax ID Code:') }} <span class="cursor-pointer text-indigo-500" @click="copyToClipboard('90011570463')">90011570463</span></p>
			<p>{{ $t('IBAN:') }} <span class="cursor-pointer text-indigo-500" @click="copyToClipboard('IT46L0503470130000000003246')">IT 46 L 05034 70130 000000003246</span></p>
			TODO: logo affiliate
		</div>
		<div class="basis-full sm:basis-1/2 lg:basis-1/5 px-3 pb-6">
			<h4 class="pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Useful Links') }}
			</h4>
			<nav>
				<FooterNavLink :href="lroute('home')">
					{{ $t('Home') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('about')">
					{{ $t('About') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('tables')">
					{{ $t('Tables') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('hotels')">
					{{ $t('Hotels') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('venue')">
					{{ $t('Venue') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('organization')">
					{{ $t('Garfaludica') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('book')">
					{{ $t('Booking Portal') }}
				</FooterNavLink>
			</nav>
		</div>
		<div class="basis-full xs:basis-1/2 lg:basis-1/5 px-3">
			<h4 class="pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Support Us') }}
			</h4>
			<p class="mt-4" v-html="$t('footer_donation_text')"></p>
			<PayPalDonateButton class="mt-6"/>
		</div>
		<div class="basis-full xs:basis-1/2 lg:basis-1/5 px-3">
			<h4 class="pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Contact Us') }}
			</h4>
			<p class="mt-4"><span class="font-bold">{{ $t('Phone:') }}</span> {{ phoneNumber }}</p>
			<p class="mt-2"><span class="font-bold">{{ $t('Email:') }}</span> {{ emailAddress }}</p>
			<p class="mt-2"><span class="font-bold">{{ $t('Website:') }}</span> {{ webAddress }}</p>
			TODO: social links
		</div>
	</div>
</template>
