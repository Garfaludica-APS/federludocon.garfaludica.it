<script setup>
import { ref, onMounted, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { getActiveLanguage } from 'laravel-vue-i18n';
import { currency } from 'maz-ui';
import { router } from '@inertiajs/vue3';

const props = defineProps({
	booking: Object,
	date: String,
	mealType: String,
	meals: Array,
	reservations: Array,
	freeMeals: Number,
});

const form = useForm({
	date: props.date,
	type: props.mealType,
	standard: 0,
	vegetarian: 0,
	vegan: 0,
});

const vegetarianInput = ref(null);
const veganInput = ref(null);

const locale = computed(() => getActiveLanguage());

const totalMeals = computed(() => {
	return form.standard + form.vegetarian + form.vegan;
});

const totalPrice = computed(() => {
	return max(form.standard * props.meals.find((meal) => meal.menu === 'standard').price +
		form.vegetarian * props.meals.find((meal) => meal.menu === 'vegetarian').price +
		form.vegan * props.meals.find((meal) => meal.menu === 'vegan').price -
		props.freeMeals * props.meals.find((meal) => meal.menu === 'standard').price, 0.0);
});

function formatPrice(value) {
	return currency(value, locale.value, { currency: 'EUR' });
}

function max(first, second) {
	return first > second ? first : second;
}

const unusedfreeMeals = computed(() => {
	return max(props.freeMeals - totalMeals.value, 0);
});

function submitForm() {
	form.patch(route('booking.meals.edit', props.booking), {
		preserveScroll: true,
		preserveState: false,
	});
}

onMounted(() => {
	props.reservations.forEach((reservation) => {
		if (reservation.meal.menu === 'standard')
			form.standard = reservation.quantity;
		else if (reservation.meal.menu === 'vegetarian')
			form.vegetarian = reservation.quantity;
		else if (reservation.meal.menu === 'vegan')
			form.vegan = reservation.quantity;
	});
	form.defaults();
});
</script>

<template>
	<form @submit.prevent="submitForm">
		<MazCard class="my-2 !w-72 2xs:!w-96 xs:!w-full" block>
			<template #title>
				<h3>{{ $t(props.mealType) }}</h3>
			</template>
			<template #subtitle>
				{{ $t('Price: :price (per person)', { price: formatPrice(props.meals.find((meal) => meal.menu === 'standard').price) }) }}
			</template>
			<template #content>
				<div class="flex flex-col sm:flex-row justify-between items-center">
					<div class="sm:basis-4/12 px-4 flex flex-col items-center">
						<h4 class="mt-2">{{ $t('Free meals included with your rooms:') }}</h4>
						<MazInputNumber
							v-model="props.freeMeals"
							:min="0"
							class="my-1"
							disabled
							size="sm"
							noButtons
							block
						/>
						<h4 class="mt-2">{{ $t('Unused free meals:') }}</h4>
						<MazInputNumber
							v-if="unusedfreeMeals > 0"
							v-model="unusedfreeMeals"
							:min="0"
							class="my-1"
							disabled
							size="sm"
							noButtons
							block
							error
						/>
						<MazInputNumber
							v-else
							v-model="unusedfreeMeals"
							:min="0"
							class="my-1"
							disabled
							size="sm"
							noButtons
							block
						/>
						<h4 class="mt-2">{{ $t('Total selected meals:') }}</h4>
						<MazInputNumber
							v-model="totalMeals"
							:min="0"
							class="my-1"
							disabled
							size="sm"
							noButtons
							block
						/>
					</div>
					<div class="sm:basis-8/12 px-4 flex flex-col items-center">
						<h4 class="text-lg my-2">{{ $t('Your menu choices:') }}</h4>
						<div class="flex flex-wrap xs:flex-nowrap items-center justify-between">
							<span class="text-right mr-4 w-32">{{ $t('Standard:') }}</span>
							<MazInputNumber
								v-model="form.standard"
								:label="$t('Standard Menu')"
								:placeholder="$t('Quantity')"
								:error="form.errors.standard ? true : false"
								:hint="form.errors.standard"
								:min="0"
								required
								class="my-2"
								color="primary"
								size="sm"
								@keydown.enter.prevent="vegetarianInput.focus()"
								block
							/>
						</div>
						<div class="flex flex-wrap xs:flex-nowrap items-center justify-between">
							<span class="text-right mr-4 w-32">{{ $t('Vegetarian:') }}</span>
							<MazInputNumber
								v-model="form.vegetarian"
								ref="vegetarianInput"
								:label="$t('Vegetarian Menu')"
								:placeholder="$t('Quantity')"
								:error="form.errors.vegetarian ? true : false"
								:hint="form.errors.vegetarian"
								:min="0"
								required
								class="my-2"
								color="secondary"
								size="sm"
								@keydown.enter.prevent="veganInput.focus()"
								block
							/>
						</div>
						<div class="flex flex-wrap xs:flex-nowrap items-center justify-between">
							<span class="text-right mr-4 w-32">{{ $t('Vegan:') }}</span>
							<MazInputNumber
								v-model="form.vegan"
								ref="veganInput"
								:label="$t('Vegan Menu')"
								:placeholder="$t('Quantity')"
								:error="form.errors.vegan ? true : false"
								:hint="form.errors.vegan"
								:min="0"
								class="my-2"
								required
								color="danger"
								size="sm"
								block
							/>
						</div>
						<MazBtn size="sm" color="transparent" class="ml-auto my-2" :disabled="form.processing" @click="form.reset()">{{ $t('Reset') }}</MazBtn>
					</div>
				</div>
			</template>
			<template #footer>
				<div class="flex justify-between items-center">
					<MazInputPrice
						v-model="totalPrice"
						:label="$t('Total price')"
						:min="0"
						currency="EUR"
						locale="locale"
						size="sm"
					/>
					<p v-if="form.errors.global" class="text-sm text-red-500">{{ form.errors.global }}</p>
					<p v-if="form.errors.date" class="text-sm text-red-500">{{ form.errors.date }}</p>
					<p v-if="form.errors.type" class="text-sm text-red-500">{{ form.errors.type }}</p>
					<MazBtn type="submit" size="lg" color="primary" class="mx-2" :loading="form.processing" :disabled="!form.isDirty">{{ $t('Save') }}</MazBtn>
				</div>
			</template>
		</MazCard>
	</form>
</template>
