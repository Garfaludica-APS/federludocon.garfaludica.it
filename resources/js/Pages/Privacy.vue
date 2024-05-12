<script setup>
import { useModal } from 'inertia-modal'

const { show, close, redirect } = useModal()
</script>

<template>
	<TransitionRoot appear as="template" :show="show">
		<Dialog as="div" class="relative z-10" @close="close">
			<TransitionChild @after-leave="redirect({ preserveScroll: true })" as="template">
				<div class="fixed inset-0 bg-black/75 transition-opacity" />
			</TransitionChild>

			<div class="fixed inset-0 overflow-y-auto">
				<div class="flex min-h-full items-center justify-center p-4 text-center">
					<TransitionChild as="template">
						<DialogPanel class="w-full max-w-lg transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white p-6 text-left align-middle shadow-xl transition-all">
							<DialogTitle as="h3" class="text-lg font-bold leading-6">
								{{ $t('Privacy Policy') }}
							</DialogTitle>
							<p class="mt-4" v-html="$t('cookie_notice_text')"></p>
							<p class="mt-4">{{ $t('We use PayPal for payments and other services. If you wish to use one of these services and pay on our website, PayPal may collect the personal data you provide, such as payment and other identifying information. PayPal uses this information to operate and improve the services it provides to us and others, including for fraud detection, harm and loss prevention, authentication, analytics related to the performance of its services, and to comply with applicable legal requirements. The processing of this information will be subject to the PayPal Privacy Statement available at PayPal.com.') }}</p>
							<ActionButton class="bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 mt-4 float-right" @click="close">
								{{ $t('Close') }}
							</ActionButton>
						</DialogPanel>
					</TransitionChild>
				</div>
			</div>
		</Dialog>
	</TransitionRoot>
</template>
