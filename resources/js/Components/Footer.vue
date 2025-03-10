<script setup>
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import { trans } from 'laravel-vue-i18n';

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import FooterNavLink from '@/Components/FooterNavLink.vue';
import SocialLink from '@/Components/SocialLink.vue';
import PayPalDonateButton from '@/Components/PaypalDonateButton.vue';

import aicslogo from '@/../../storage/images/aics-logo.png';
import federludomark from '@/../../storage/images/federludo-mark.png';
import tdglogo from '@/../../storage/images/tdg-round-logo.png';
import comunestemma from '@/../../storage/images/stemma-castelnuovo.svg';

const page = usePage();

const phoneNumber = '+39 324 746 0610';
const phoneNumberHref = 'tel:' + phoneNumber.replace(/\s/g, '');
const emailAddress = 'info@garfaludica.it';
const emailAddressHref = 'mailto:' + emailAddress;
const webAddress = 'https://www.garfaludica.it';
const webAddressNoProtocol = webAddress.replace(/^https?:\/\//, '');
const taxIdCode = '90011570463';
const iban = 'IT 46 L 05034 70130 000000003246';
const ibanNoSpaces = iban.replace(/\s/g, '');
const tdgLink = 'https://www.goblins.net/affiliate/tana-dei-goblin-castelnuovo-di-garfagnana';
const federludoLink = 'https://www.federludo.it/associazioni/garfaludica-aps';
const aicsLink = 'https://www.aics.it';
const comuneLink = 'https://comune.castelnuovodigarfagnana.lu.it';

function showToast(msg, timeout = 3000)
{
	const t = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
	toast(msg, { autoClose: timeout, theme: t, position: 'bottom-center', type: 'info' });
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
			<p>{{ $t('Tax ID Code') }}: <span class="cursor-pointer text-indigo-500" @click="copyToClipboard(taxIdCode)">{{ taxIdCode }}</span></p>
			<p>{{ $t('IBAN') }}: <span class="cursor-pointer text-indigo-500" @click="copyToClipboard(ibanNoSpaces)">{{ iban }}</span></p>
			<!--sse-->
			<p class="text-sm" v-html="$t('footer_org_contact_text')"></p>
			<!--/sse-->
			<div class="mt-6 flex space-x-6 flex-wrap">
				<a rel="external noopener nofollow" :href="tdgLink" target="_blank">
					<img :src="tdglogo" alt="Logo Tana dei Goblin" class="inline-block h-12 w-auto" />
				</a>
				<a rel="external noopener nofollow" :href="federludoLink" target="_blank">
					<img :src="federludomark" alt="Logo Affiliata Federludo" class="inline-block h-12 w-auto" />
				</a>
				<a rel="external noopener nofollow" :href="aicsLink" target="_blank">
					<img :src="aicslogo" alt="Logo Associazione Italiana Cultura e Sport" class="inline-block h-12 w-auto" />
				</a>
			</div>
			<a rel="external noopener nofollow" :href="comuneLink" target="_blank" class="flex align-center items-center mt-4">
				<img :src="comunestemma" alt="Stemma del Comune di Castelnuovo di Garfagnana" class="w-16 h-auto inline-block mr-4" />
				<p class="inline max-w-48 text-wrap !inline-block">{{ $t('Con il Patrocinio del Comune di Castelnuovo di Garfagnana') }}</p>
			</a>
		</div>
		<div class="basis-full sm:basis-1/2 lg:basis-1/5 px-3 pb-6">
			<h4 class="pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Useful Links') }}
			</h4>
			<nav>
				<FooterNavLink :href="route(page.props.rp + 'home')">
					{{ $t('Home') }}
				</FooterNavLink>
				<FooterNavLink :href="route(page.props.rp + 'about')">
					{{ $t('About') }}
				</FooterNavLink>
				<FooterNavLink :href="route(page.props.rp + 'larp')">
					{{ $t('Stelle Nere &amp; Strane Lune') }}
				</FooterNavLink>
				<FooterNavLink :href="route(page.props.rp + 'organization')">
					{{ $t('The Association') }}
				</FooterNavLink>
				<FooterNavLink :href="route(page.props.rp + 'contact')">
					{{ $t('Support') }}
				</FooterNavLink>
			</nav>
		</div>
		<div class="basis-full xs:basis-1/2 lg:basis-1/5 px-3">
			<h4 class="pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Useful Links') }}
			</h4>
			<nav>
				<FooterNavLink href="https://t.me/federludocongarfagnana" external>
					{{ $t('Telegram Group') }}
				</FooterNavLink>
				<FooterNavLink :href="route(page.props.rp + 'modal.privacy', { redirect: route().current() })" preserve-state preserve-scroll>
					{{ $t('Privacy Policy') }}
				</FooterNavLink>
				<FooterNavLink :href="webAddress" external>
					{{ $t('Garfaludica APS') }}
				</FooterNavLink>
				<FooterNavLink href="https://t.me/associazionegarfaludica" external>
					{{ $t('Garfaludica Telegram') }}
				</FooterNavLink>
			</nav>
		</div>
		<div class="basis-full xs:basis-1/2 lg:basis-1/5 px-3">
			<h4 class="pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Contact Us') }}
			</h4>
			<!--sse-->
			<p class="mt-4"><span class="font-bold">{{ $t('Phone') }}:</span> <a rel="noopener" :href="phoneNumberHref" target="_blank">{{ phoneNumber }}</a></p>
			<p class="mt-2"><span class="font-bold">{{ $t('Email') }}:</span> <a rel="noopener" :href="emailAddressHref" target="_blank">{{ emailAddress }}</a></p>
			<!--/sse-->
			<p class="mt-2"><span class="font-bold">{{ $t('Website') }}:</span> <a rel="external noopener" :href="webAddress" target="_blank">{{ webAddressNoProtocol }}</a></p>

			<div class="flex justify-around mt-6">
				<SocialLink :href="webAddress" icon="web" />
				<SocialLink href="https://t.me/associazionegarfaludica" icon="telegram" />
				<SocialLink href="https://facebook.com/garfaludica" icon="facebook" />
				<SocialLink href="https://instagram.com/garfaludica" icon="instagram" />
			</div>
			<div class="flex justify-around mt-2">
				<SocialLink href="https://discord.gg/vMUct2bxSe" icon="discord" />
				<SocialLink href="https://github.com/Garfaludica-APS" icon="github" />
				<SocialLink href="https://t.me/GarfaludicaAPSbot" icon="robot" />
				<SocialLink href="https://maps.app.goo.gl/2idX8ujXq7p7jAqTA" icon="location" />
			</div>

			<h4 class="mt-4 pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Support Us') }}
			</h4>
			<p class="mt-4 text-xs hyphens-auto" v-html="$t('footer_donation_text')"></p>
			<PayPalDonateButton class="mt-6"/>
		</div>
	</div>
</template>
