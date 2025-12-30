# Vue Component Duplication Guide

## Overview

You have **3 complete template components** for Birth certificates:
1. ✅ `resources/js/Pages/archive/birth/Index.vue` - Complete listing with search, filters, pagination
2. ✅ `resources/js/Pages/archive/birth/Create.vue` - Complete create form with file upload
3. ✅ `resources/js/Pages/archive/BulkImport.vue` - Shared bulk import (works for all types)

**You need to create 10 more components** by duplicating and modifying the Birth templates.

---

## Components to Create

### Marriage Certificate Components (4 files)
1. `resources/js/Pages/archive/marriage/Index.vue`
2. `resources/js/Pages/archive/marriage/Create.vue`
3. `resources/js/Pages/archive/marriage/Edit.vue`
4. `resources/js/Pages/archive/marriage/Show.vue`

### Death Certificate Components (4 files)
5. `resources/js/Pages/archive/death/Index.vue`
6. `resources/js/Pages/archive/death/Create.vue`
7. `resources/js/Pages/archive/death/Edit.vue`
8. `resources/js/Pages/archive/death/Show.vue`

### Birth Certificate Components (2 files)
9. `resources/js/Pages/archive/birth/Edit.vue`
10. `resources/js/Pages/archive/birth/Show.vue`

---

## Find & Replace Guide

### For Marriage Components

Copy each Birth file and perform these replacements:

| Find | Replace With |
|------|-------------|
| `birth` (lowercase) | `marriage` |
| `Birth` (capitalized) | `Marriage` |
| `birth_year` | `marriage_year` |
| `birth_month` | `marriage_month` |
| `birth_day` | `marriage_day` |
| `Birth Date` | `Marriage Date` |
| `Birth archive` | `Marriage archive` |
| `birth certificate` | `marriage certificate` |
| `archive.birth` | `archive.marriage` |
| `birthArchive` | `marriageArchive` |

**Example:**
```typescript
// Birth Index.vue line
const birth_year = ref(props.filters?.birth_year || '');

// Becomes in Marriage Index.vue
const marriage_year = ref(props.filters?.marriage_year || '');
```

---

### For Death Components

Copy each Birth file and perform these replacements:

| Find | Replace With |
|------|-------------|
| `birth` (lowercase) | `death` |
| `Birth` (capitalized) | `Death` |
| `birth_year` | `death_year` |
| `birth_month` | `death_month` |
| `birth_day` | `death_day` |
| `Birth Date` | `Death Date` |
| `Birth archive` | `Death archive` |
| `birth certificate` | `death certificate` |
| `archive.birth` | `archive.death` |
| `birthArchive` | `deathArchive` |

---

## Step-by-Step Process

### Step 1: Create Marriage Index.vue

1. Copy `resources/js/Pages/archive/birth/Index.vue`
2. Save as `resources/js/Pages/archive/marriage/Index.vue`
3. Use Find & Replace (Ctrl+H in most editors) with the Marriage replacements above
4. Verify route names are correct (e.g., `route('archive.marriage.index')`)
5. ✅ **AppLayout is already included** - The Birth template has been updated with the correct layout!

### Step 2: Create Marriage Create.vue

1. Copy `resources/js/Pages/archive/birth/Create.vue`
2. Save as `resources/js/Pages/archive/marriage/Create.vue`
3. Use Find & Replace with the Marriage replacements
4. Update the header text: "Add Marriage Archive Certificate"

### Step 3: Create Marriage Edit.vue

**Template:** Use Create.vue as a starting point

```vue
<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Props {
  certificate: {
    id: number;
    reg_year: string;
    reg_no: string;
    marriage_year: number;
    marriage_month: number;
    marriage_day: number;
    first_name: string;
    middle_name: string;
    last_name: string;
    notes: string;
    folder_path: string;
    file_name: string;
  };
}

const props = defineProps<Props>();

const form = useForm({
  file: null as File | null,
  reg_year: props.certificate.reg_year,
  reg_no: props.certificate.reg_no,
  marriage_year: props.certificate.marriage_year,
  marriage_month: props.certificate.marriage_month,
  marriage_day: props.certificate.marriage_day,
  first_name: props.certificate.first_name,
  middle_name: props.certificate.middle_name || '',
  last_name: props.certificate.last_name,
  notes: props.certificate.notes || '',
});

const fileInput = ref<HTMLInputElement | null>(null);
const fileName = ref('');

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.file = target.files[0];
    fileName.value = target.files[0].name;
  }
}

function submit() {
  form.put(route('archive.marriage.certificates.update', props.certificate.id), {
    preserveScroll: true,
  });
}
</script>

<template>
  <!-- Use same template as Create.vue but with: -->
  <!-- 1. Title: "Edit Marriage Archive Certificate" -->
  <!-- 2. File input is optional (not required) -->
  <!-- 3. Add note: "Leave empty to keep existing file" -->
  <!-- 4. form.put() instead of form.post() -->
  <!-- 5. Include certificate ID in route -->
</template>
```

**Key Differences from Create:**
- Accepts `certificate` prop with existing data
- Pre-fills form with existing values
- File upload is optional (`nullable` in request)
- Uses `form.put()` instead of `form.post()`
- Button text: "Update Certificate" instead of "Save Certificate"

### Step 4: Create Marriage Show.vue

**Template:**

```vue
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Download, Edit, Trash2 } from 'lucide-vue-next';

interface Props {
  certificate: {
    id: number;
    full_name: string;
    reg_year: string;
    reg_no: string;
    marriage_year: number;
    marriage_month: number;
    marriage_day: number;
    formatted_date: string;
    notes: string;
    folder_path: string;
    file_name: string;
    created_at: string;
    updated_at: string;
    creator?: { name: string };
    updater?: { name: string };
  };
}

const props = defineProps<Props>();

function downloadCertificate() {
  window.open(route('archive.marriage.download', props.certificate.id), '_blank');
}

function editCertificate() {
  router.visit(route('archive.marriage.certificates.edit', props.certificate.id));
}

function deleteCertificate() {
  if (confirm('Are you sure you want to delete this certificate?')) {
    router.delete(route('archive.marriage.certificates.destroy', props.certificate.id));
  }
}
</script>

<template>
  <div class="mx-auto max-w-4xl p-6">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Marriage Certificate Details</h1>
        <p class="mt-1 text-gray-600">{{ certificate.full_name }}</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" @click="downloadCertificate">
          <Download class="mr-2 h-4 w-4" />
          Download
        </Button>
        <Button variant="outline" @click="editCertificate">
          <Edit class="mr-2 h-4 w-4" />
          Edit
        </Button>
        <Button variant="destructive" @click="deleteCertificate">
          <Trash2 class="mr-2 h-4 w-4" />
          Delete
        </Button>
      </div>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>Certificate Information</CardTitle>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500">Registration Number</p>
            <p class="mt-1 text-sm text-gray-900">
              {{ certificate.reg_year }}/{{ certificate.reg_no }}
            </p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Marriage Date</p>
            <p class="mt-1 text-sm text-gray-900">{{ certificate.formatted_date }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Full Name</p>
            <p class="mt-1 text-sm text-gray-900">{{ certificate.full_name }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">File Name</p>
            <p class="mt-1 text-sm text-gray-900">{{ certificate.file_name }}</p>
          </div>
        </div>

        <div v-if="certificate.notes">
          <p class="text-sm font-medium text-gray-500">Notes</p>
          <p class="mt-1 text-sm text-gray-900">{{ certificate.notes }}</p>
        </div>

        <div class="border-t pt-4">
          <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
            <div>
              <p>Created: {{ new Date(certificate.created_at).toLocaleString() }}</p>
              <p v-if="certificate.creator">By: {{ certificate.creator.name }}</p>
            </div>
            <div>
              <p>Updated: {{ new Date(certificate.updated_at).toLocaleString() }}</p>
              <p v-if="certificate.updater">By: {{ certificate.updater.name }}</p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <div class="mt-6">
      <Button variant="outline" @click="router.visit(route('archive.marriage.index'))">
        Back to List
      </Button>
    </div>
  </div>
</template>
```

### Step 5-8: Repeat for Death Components

Follow the same process as Steps 1-4, but use the Death replacements instead.

---

## Quick Reference: Route Names

### Birth Routes
- Index: `archive.birth.index`
- Create: `archive.birth.certificates.create`
- Store: `archive.birth.certificates.store`
- Show: `archive.birth.certificates.show`
- Edit: `archive.birth.certificates.edit`
- Update: `archive.birth.certificates.update`
- Delete: `archive.birth.certificates.destroy`
- Restore: `archive.birth.restore`
- Download: `archive.birth.download`
- Bulk Import: `archive.birth.bulk-import`

### Marriage Routes
Replace `birth` with `marriage` in all routes above.
Replace `birthArchive` with `marriageArchive` in route parameters.

### Death Routes
Replace `birth` with `death` in all routes above.
Replace `birthArchive` with `deathArchive` in route parameters.

---

## Testing Checklist

After creating all components, test:

- [ ] Birth Index page loads
- [ ] Marriage Index page loads
- [ ] Death Index page loads
- [ ] Search filters work on all index pages
- [ ] Date filters work (year/month/day)
- [ ] Create forms submit successfully
- [ ] Edit forms load existing data
- [ ] Edit forms update successfully
- [ ] Show pages display all information
- [ ] Delete button soft deletes records
- [ ] Restore button appears for deleted records
- [ ] Download button opens S3 file
- [ ] Bulk import works for all types
- [ ] Sidebar navigation links work
- [ ] Permissions restrict unauthorized access

---

## Common Issues & Solutions

### Issue: "Route [archive.marriage.index] not defined"
**Solution:** Clear route cache: `php artisan route:clear`

### Issue: Components not showing in browser
**Solution:** Run `npm run dev` to rebuild frontend assets

### Issue: TypeScript errors
**Solution:** Check interface definitions match controller response structure

### Issue: 404 errors
**Solution:** Verify routes are registered in `routes/web.php`

---

## Pro Tips

1. **Use VS Code Multi-Cursor**: Select all instances of "birth" and change them simultaneously
2. **Test incrementally**: Create one type fully (Marriage) before moving to Death
3. **Use browser DevTools**: Check Network tab for API responses and errors
4. **Keep templates consistent**: Don't add extra features to one type that others don't have
5. **Backup before bulk changes**: Use Git to commit before major find/replace operations

---

## Need Help?

Check these files for reference:
- ✅ `resources/js/Pages/archive/birth/Index.vue` - Complete working example
- ✅ `resources/js/Pages/archive/birth/Create.vue` - Form example
- ✅ `app/Modules/Members/Http/Controllers/MarriageArchiveCertificateController.php` - Backend logic
- ✅ `routes/archive_certificates.php` - All route definitions
