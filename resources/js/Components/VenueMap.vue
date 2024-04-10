<script setup>
import { GoogleMap, Marker, InfoWindow } from 'vue3-google-map';

const center = { lat: 44.170957620462325, lng: 10.376145170266154 };
const iseraMarker = {
	position: { lat: 44.20491359849247, lng: 10.368916260935093 },
	label: 'I',
	title: 'Rifugio Isera Orecchiella'
};
const panoramicMarker = {
	position: { lat: 44.18157048623851, lng: 10.378256511564778 },
	label: 'P',
	title: 'Hotel Ristorante Panoramico'
};
const braccicortiMarker = {
	position: { lat: 44.14309853812364, lng: 10.37826403855039 },
	label: 'A',
	title: 'Agriturismo Braccicorti'
};
const camperMarker = {
	position: { lat: 44.182512022223484, lng: 10.374993350828136 },
	label: 'C',
	title: 'Area Camper'
};
</script>

<template>
	<GoogleMap class="w-full h-[750px]"
		api-key="AIzaSyDmCRFGUtUffG08T2ylnEAW3I9HdJrNcFI"
		:center="center"
		:zoom="13"
	>
		<Marker :options="iseraMarker">
			<InfoWindow>
				<article>
					<h1 class="text-lg font-bold">{{ $t('hotel_name_Isera Refuge') }}</h1>
					<h2 class="font-bold">{{ $t('Address') }}:</h2>
					<p>{{ $t('Near Riserva Naturale Orecchiella, 55030 Corfino (Villa Collemandina), Lucca') }}</p>
					<h2 class="font-bold">{{ $t('Contacts')}}:</h2>
					<p>{{ $t('Tel: :tel', {tel: '+39 333 643 1313'}) }}</p>
					<p>{{ $t('Email: :email', {email: 'iseraorecchiella@gmail.com'}) }}</p>
					<h2 class="font-bold">{{ $t('Website')}}:</h2>
					<p>www.rifugioisera.com</p>
				</article>
			</InfoWindow>
		</Marker>
		<Marker :options="panoramicMarker">
			<InfoWindow>
				<article>
					<h1 class="text-lg font-bold">{{ $t('hotel_name_Panoramic Hotel') }}</h1>
					<h2 class="font-bold">{{ $t('Address') }}:</h2>
					<p>{{ $t('Street Fondo la Terra, 9, 55030 Corfino (Villa Collemandina), Lucca') }}</p>
					<h2 class="font-bold">{{ $t('Contacts')}}:</h2>
					<p>{{ $t('Tel: :tel', {tel: '+39 320 311 8664'}) }}</p>
					<p>{{ $t('Email: :email', {email: 'info@albergopanoramico.it'}) }}</p>
					<h2 class="font-bold">{{ $t('Website')}}:</h2>
					<p>www.albergopanoramico.it</p>
				</article>
			</InfoWindow>
		</Marker>
		<Marker :options="braccicortiMarker">
			<InfoWindow>
				<article>
					<h1 class="text-lg font-bold">{{ $t('hotel_name_Braccicorti Farmhouse') }}</h1>
					<h2 class="font-bold">{{ $t('Address') }}:</h2>
					<p>{{ $t('Braccicorti Location, 38/A, 55036 Pontecosi (Pieve Fosciana), Lucca') }}</p>
					<h2 class="font-bold">{{ $t('Contacts')}}:</h2>
					<p>{{ $t('Tel: :tel', {tel: '+39 371 528 1155'}) }}</p>
					<p>{{ $t('Email: :email', {email: 'info@braccicorti.it'}) }}</p>
					<h2 class="font-bold">{{ $t('Website')}}:</h2>
					<p>www.braccicorti.it</p>
				</article>
			</InfoWindow>
		</Marker>
		<Marker :options="camperMarker">
			<InfoWindow>
				<article>
					<h1 class="text-lg font-bold">{{ $t('Camper Equipped Area') }}</h1>
					<h2 class="font-bold">{{ $t('Address') }}:</h2>
					<p>{{ $t('Parking Street Madonna, 55030 Corfino (Villa Collemandina), Lucca') }}</p>
				</article>
			</InfoWindow>
		</Marker>
	</GoogleMap>
</template>
