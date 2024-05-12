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
								{{ $t('Refund Policy') }}
							</DialogTitle>
							<p class="mt-4">{{ $t('After placing an order, you may ask for a total refund any time before the start of the event. Partial refund are not allowed. If you need to change your order, please ask a total refund and then place a new order. Refunds are processed manually in a couple of days.') }}</p>
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
