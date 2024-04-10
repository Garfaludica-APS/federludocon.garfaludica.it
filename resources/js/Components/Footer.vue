<script setup>
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import { trans } from 'laravel-vue-i18n';

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import FooterNavLink from '@/Components/FooterNavLink.vue';
import SocialLink from '@/Components/SocialLink.vue';
import PayPalDonateButton from '@/Components/PaypalDonateButton.vue';

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
const aicsLink = 'https://www.aics.it/';

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
					<img :src="imageurl('tdg-round-logo.png')" alt="Logo Tana dei Goblin" class="inline-block h-12 w-auto" />
				</a>
				<a rel="external noopener nofollow" :href="federludoLink" target="_blank">
					<img :src="imageurl('federludo-logo.png')" alt="Logo Affiliata Federludo" class="inline-block h-12 w-auto" />
				</a>
				<a rel="external noopener nofollow" :href="aicsLink" target="_blank">
					<img :src="imageurl('aics-logo.png')" alt="Logo Associazione Italiana Cultura e Sport" class="inline-block h-12 w-auto" />
				</a>
			</div>
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
				<FooterNavLink :href="lroute('hotels')">
					{{ $t('Hotels') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('venue')">
					{{ $t('Venue') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('organization')">
					{{ $t('The Association') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('contact')">
					{{ $t('Support') }}
				</FooterNavLink>
			</nav>
		</div>
		<div class="basis-full xs:basis-1/2 lg:basis-1/5 px-3">
			<h4 class="pb-2 border-b-2 border-indigo-500 text-lg uppercase font-extrabold">
				{{ $t('Useful Links') }}
			</h4>
			<nav>
				<FooterNavLink v-if="page.props.settings.portalOpen" :href="lroute('book')">
					{{ $t('Booking Portal') }}
				</FooterNavLink>
				<FooterNavLink href="https://t.me/gobcongarfagnana" external>
					{{ $t('Telegram Group') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('modal.refund', { redirect: lroute().current() })" preserve-state preserve-scroll>
					{{ $t('Refund Policy') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('modal.terms', { redirect: lroute().current() })" preserve-state preserve-scroll>
					{{ $t('Terms of Service') }}
				</FooterNavLink>
				<FooterNavLink :href="lroute('modal.privacy', { redirect: lroute().current() })" preserve-state preserve-scroll>
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
