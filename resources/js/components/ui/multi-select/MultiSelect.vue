<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { ChevronDown, X } from 'lucide-vue-next';
import { ComboboxAnchor, ComboboxContent, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxLabel, ComboboxRoot, ComboboxTrigger, ComboboxViewport, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText, TagsInputRoot } from 'reka-ui';
import InputError from '@/components/InputError.vue';

/**
 * Defines the structure of an option in the MultiSelect component
 */
export interface Option {
  id: string | number;
  label: string;
}

interface MultiSelectProps {
  /**
   * The currently selected option IDs (used with v-model)
   */
  modelValue: (string | number)[];

  /**
   * The available options to select from
   */
  options: Option[];

  /**
   * Placeholder text for the input field
   */
  placeholder?: string;

  /**
   * Error message to display below the MultiSelect
   */
  errorMessage?: string;

  /**
   * Placeholder for the search input
   */
  searchPlaceholder?: string;

  /**
   * Message to display when no options are available
   */
  emptyMessage?: string;

  /**
   * Label for the options group
   */
  groupLabel?: string;

  /**
   * Disable the MultiSelect
   */
  disabled?: boolean;
}

const props = withDefaults(defineProps<MultiSelectProps>(), {
  placeholder: 'Type to search...',
  searchPlaceholder: 'Type to search...',
  emptyMessage: 'No options available',
  groupLabel: 'Options',
  disabled: false
});

const emit = defineEmits(['update:modelValue']);

// Search term for combobox
const searchTerm = ref('');

// Reset search term when selected options change
watch(() => props.modelValue, () => {
  searchTerm.value = '';
}, { deep: true });

// Filtered options that haven't been selected yet
const filteredOptions = computed(() => {
  if (!searchTerm.value) {
    return props.options.filter(option =>
      !props.modelValue.includes(String(option.id)) &&
      !props.modelValue.includes(option.id)
    );
  }

  const search = searchTerm.value.toLowerCase();
  return props.options.filter(option =>
    (!props.modelValue.includes(String(option.id)) &&
      !props.modelValue.includes(option.id)) &&
    option.label.toLowerCase().includes(search)
  );
});

// Get option label by ID
function getOptionLabel(id: any): string {
  const stringId = String(id);
  const option = props.options.find(opt => String(opt.id) === stringId);
  return option ? option.label : stringId;
}

// Use computed to manage internal model for v-model binding
// Convert all values to strings to ensure compatibility with AcceptableInputValue
const selectedOptions = computed({
  get: () => props.modelValue.map(id => String(id)),
  set: (value: string[]) => {
    // Convert back to original type if needed
    const convertedValues = value.map(v => {
      const original = props.options.find(opt => String(opt.id) === v);
      return original ? original.id : v;
    });
    emit('update:modelValue', convertedValues);
  }
});
</script>

<template>
  <div class="relative">
    <ComboboxRoot v-model="selectedOptions" v-model:search-term="searchTerm" multiple class="relative"
      :disabled="disabled">
      <ComboboxAnchor
        class="w-full inline-flex items-center justify-between rounded-lg p-1 px-2 h-9 text-sm leading-none gap-[5px] bg-background text-foreground shadow-xs border border-input hover:bg-accent/50 focus:shadow-[0_0_0_2px] focus:shadow-ring outline-none disabled:opacity-50 disabled:cursor-not-allowed">
        <TagsInputRoot v-slot="{ modelValue: tags }" v-model="selectedOptions" delimiter=""
          class="flex gap-2 items-center rounded-lg flex-wrap">
          <TagsInputItem v-for="optionId in tags" :key="String(optionId)" :value="optionId"
            class="flex items-center justify-center gap-2 text-primary-foreground bg-primary rounded px-2 py-0">
            <TagsInputItemText class="text-sm">{{ getOptionLabel(optionId) }}</TagsInputItemText>
            <TagsInputItemDelete class="hover:bg-primary-foreground/10 rounded">
              <X class="h-3.5 w-3.5" />
            </TagsInputItemDelete>
          </TagsInputItem>

          <ComboboxInput as-child>
            <TagsInputInput :placeholder="placeholder"
              class="text-xs focus:outline-none flex-1 rounded bg-transparent px-1" @keydown.enter.prevent />
          </ComboboxInput>
        </TagsInputRoot>

        <ComboboxTrigger>
          <ChevronDown class="h-3.5 w-3.5" />
        </ComboboxTrigger>
      </ComboboxAnchor>

      <ComboboxContent
        class="absolute z-50 w-full mt-2 bg-background overflow-hidden rounded shadow-md border border-border will-change-[opacity,transform] data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade">
        <ComboboxViewport class="p-1">
          <ComboboxEmpty class="text-muted-foreground text-xs font-medium text-center py-2">
            {{ emptyMessage }}
          </ComboboxEmpty>

          <ComboboxGroup>
            <ComboboxLabel class="px-2 text-xs leading-6 text-muted-foreground">
              {{ groupLabel }}
            </ComboboxLabel>

            <ComboboxItem v-for="option in filteredOptions" :key="option.id"
              class="text-sm leading-none text-foreground rounded flex items-center h-8 pr-9 pl-8 relative select-none data-[disabled]:text-muted-foreground data-[disabled]:pointer-events-none data-[highlighted]:outline-none data-[highlighted]:bg-primary data-[highlighted]:text-primary-foreground"
              :value="option.id">
              <ComboboxItemIndicator class="absolute left-2 flex items-center justify-center">
                <X class="h-3.5 w-3.5" />
              </ComboboxItemIndicator>
              <span>{{ option.label }}</span>
            </ComboboxItem>
          </ComboboxGroup>
        </ComboboxViewport>
      </ComboboxContent>
    </ComboboxRoot>
    <InputError v-if="errorMessage" :message="errorMessage" class="mt-1" />
  </div>
</template>