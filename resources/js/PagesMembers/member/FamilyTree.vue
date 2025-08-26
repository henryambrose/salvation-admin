<template>
  <div 
    id="family-root" 
    class="p-4 md:p-6"
    :style="{ minHeight: containerHeight }"
  >
    <div v-if="loading" class="py-10 text-center text-gray-500">Loading family tree...</div>

    <div v-else-if="!familyTree || !hasAnyMembers" class="py-16 text-center">
      <div class="mx-auto max-w-sm">
        <div class="mb-4">
          <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
            />
          </svg>
        </div>
        <h3 class="mb-2 text-lg font-medium text-gray-900">No Family Tree Available</h3>
        <p class="mb-4 text-gray-500">This family doesn't have any members assigned yet.</p>
        <button
          @click="goBack"
          class="inline-flex items-center rounded-md border border-gray-300 bg-[#ffffff] px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
        >
          <svg class="mr-2 h-[1rem] w-[1rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Go Back
        </button>
      </div>
    </div>

    <div v-else class="space-y-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button
            type="button"
            class="inline-flex items-center rounded-md border border-gray-200 bg-[#ffffff] px-3 py-1.5 text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
            @click="goBack"
            aria-label="Go back"
          >
            <svg
              class="mr-2 h-[1rem] w-[1rem]"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="15 18 9 12 15 6"></polyline>
              <line x1="9" y1="12" x2="21" y2="12"></line>
            </svg>
            Back
          </button>
          <h2 class="text-xl font-semibold">Family Tree</h2>
        </div>
        <div class="flex items-center gap-3">
          <div class="text-sm text-gray-500">
            {{ displayNameWithNo(person) || 'Unknown' }}
          </div>
          <!-- Download PNG Buttons -->
          <div class="flex items-center gap-2">
            <button 
              @click="downloadPNG(true)"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
            >
              <svg class="h-[1rem] w-[1rem] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Save PNG
            </button>
          </div>
        </div>
      </div>
      <section v-if="greatGreatGrandparents.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Great Great Grandparents</h3>
        <div class="grid-autofit">
          <div v-for="p in greatGreatGrandparents" :key="p.member.id" class="card-neo tone-amber">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(p.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(p.member) }}</div>
                <div class="text-xs text-gray-500">{{ p.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-amber">{{ p.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="greatGrandparents.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Great Grandparents</h3>
        <div class="grid-autofit">
          <div v-for="p in greatGrandparents" :key="p.member.id" class="card-neo tone-amber">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(p.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(p.member) }}</div>
                <div class="text-xs text-gray-500">{{ p.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-amber">{{ p.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="grandparents.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Grandparents</h3>
        <div class="grid-autofit">
          <div v-for="p in grandparents" :key="p.member.id" class="card-neo tone-amber">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(p.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(p.member) }}</div>
                <div class="text-xs text-gray-500">{{ p.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-amber">{{ p.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="parents.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Parents</h3>
        <div class="grid-autofit">
          <div v-for="p in parents" :key="p.member.id" class="card-neo tone-blue">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(p.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(p.member) }}</div>
                <div class="text-xs text-gray-500">{{ p.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-blue">{{ p.member.source }} </span>
          </div>
        </div>
      </section>
      <section class="space-y-3">
        <h3 class="section-title">
          Primary — {{ displayNameWithNo(person) || 'Unknown' }} <span v-if="person?.family_no" class="text-slate-400">({{ person.family_no }})</span>
        </h3>
        <div class="grid-autofit">
          <div :class="['card-neo', primaryCoupleColor || 'tone-indigo']">
            <div class="flex items-center space-x-3">
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-base font-semibold text-indigo-700">
                {{ initials(person) }}
              </div>
              <div>
                <div class="name font-semibold">{{ displayNameWithNo(person) }}</div>
                <div class="rounded-r-md border-l-4 border-blue-500 bg-blue-50 px-3 py-2">
                  <div class="text-sm font-semibold text-blue-800">
                    Current Person<span v-if="personRelationLabel"> — {{ personRelationLabel }}</span>
                  </div>
                </div>
              </div>
            </div>
            <span class="badge badge-indigo">{{ person?.source }}</span>
          </div>

          <div v-if="spouse" :class="['card-neo', primaryCoupleColor || 'tone-pink']">
            <div class="flex items-center space-x-3">
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-pink-50 text-base font-semibold text-pink-700">
                {{ initials(spouse.member) }}
              </div>
              <div>
                <div class="name font-semibold">{{ displayNameWithNo(spouse.member) }}</div>
                <div class="text-xs text-gray-500">{{ spouse.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-pink">{{ spouse.member.source }} </span>
          </div>
        </div>
      </section>
      <section v-if="siblings.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Siblings</h3>
        <div class="grid-autofit">
          <div v-for="s in siblings" :key="s.member.id" :class="['card-neo', s.coupleColor ? s.coupleColor : s.singleMemberColor]">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(s.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(s.member) }}</div>
                <div class="text-xs text-gray-500">{{ s.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-violet">{{ s.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="children.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Children</h3>
        <div class="grid-autofit">
          <div v-for="c in children" :key="c.member.id" :class="['card-neo', c.coupleColor ? c.coupleColor : c.singleMemberColor]">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(c.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(c.member) }}</div>
                <div class="text-xs text-gray-500">{{ c.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-emerald">{{ c.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="grandchildren.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Grandchildren</h3>
        <div class="grid-autofit">
          <div v-for="gc in grandchildren" :key="gc.member.id" :class="['card-neo', gc.coupleColor ? gc.coupleColor : gc.singleMemberColor]">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(gc.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(gc.member) }}</div>
                <div class="text-xs text-gray-500">{{ gc.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-teal">{{ gc.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="greatGrandchildren.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Great Grandchildren</h3>
        <div class="grid-autofit">
          <div v-for="gc in greatGrandchildren" :key="gc.member.id" :class="['card-neo', gc.coupleColor ? gc.coupleColor : gc.singleMemberColor]">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(gc.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(gc.member) }}</div>
                <div class="text-xs text-gray-500">{{ gc.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-teal">{{ gc.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="greatGreatGrandchildren.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Great Great Grandchildren</h3>
        <div class="grid-autofit">
          <div
            v-for="gc in greatGreatGrandchildren"
            :key="gc.member.id"
            :class="['card-neo', gc.coupleColor ? gc.coupleColor : gc.singleMemberColor]"
          >
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(gc.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(gc.member) }}</div>
                <div class="text-xs text-gray-500">{{ gc.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-teal">{{ gc.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="familyMembers.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">Other Family Members</h3>
        <div class="grid-autofit">
          <div v-for="fm in familyMembers" :key="fm.member.id" :class="['card-neo', fm.coupleColor ? fm.coupleColor : fm.singleMemberColor]">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(fm.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(fm.member) }}</div>
                <div class="text-xs text-gray-500">{{ fm.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-slate">{{ fm.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="externalMembers.length" class="space-y-3">
        <h3 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">External Members</h3>
        <div class="grid-autofit">
          <div v-for="em in externalMembers" :key="em.member.id" class="card-neo tone-orange">
            <div class="flex items-center space-x-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold">
                {{ initials(em.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(em.member) }}</div>
                <div class="text-xs text-gray-500">{{ em.relationship }}</div>
              </div>
            </div>
            <span class="badge badge-orange">External</span>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import html2canvas from 'html2canvas'
import { toPng } from 'html-to-image'
import { saveAs } from 'file-saver'

async function downloadPNG(full = false) {
  try {
    const node = document.getElementById('family-root')
    if (!node) return alert('Capture root not found')

    // Optional: wait for webfonts/icons to load so glyphs don’t disappear
    if (document.fonts?.ready) await document.fonts.ready

    // Temporarily tweak styles (avoid sticky headers, animations in snapshot)
    const cleanup = prepareForSnapshot(node)

    const dataUrl = await toPng(node, {
      cacheBust: true,
      pixelRatio: 2,           // sharper image
      backgroundColor: '#fff', // if your page bg is transparent
      // Capture more content by temporarily expanding the container
      ...(full ? expandForFull(node) : {}),
      filter: (el) => !el.classList?.contains('no-print'),
    })

    cleanup?.()
    saveAs(dataUrl, `family-${props.type}-${props.id}.png`)
  } catch (e) {
    console.error(e)
    alert('Failed to capture image. See console for details.')
  }
}
/** Make the snapshot stable: pause animations, un-sticky, etc. */
function prepareForSnapshot(root) {
  const prev = new Map()
  const elts = root.querySelectorAll('*')
  elts.forEach(el => {
    const s = el.style
    prev.set(el, {
      pos: s.position, ani: s.animation, tran: s.transition,
      filter: s.filter, will: s.willChange
    })
    s.animation = 'none'
    s.transition = 'none'
    if (getComputedStyle(el).position === 'sticky') s.position = 'static'
    // optional: remove heavy filters/shadows if you see artifacts
    // s.filter = 'none'; s.willChange = 'auto'
  })
  return () => {
    elts.forEach(el => {
      const p = prev.get(el)
      if (!p) return
      const s = el.style
      s.position = p.pos
      s.animation = p.ani
      s.transition = p.tran
      s.filter = p.filter
      s.willChange = p.will
    })
  }
}

/** Make the node height flexible based on content */
function expandForFull(node) {
  const prev = { height: node.style.height, overflow: node.style.overflow, minHeight: node.style.minHeight }
  
  // Calculate the actual content height
  const contentHeight = node.scrollHeight
  const viewportHeight = window.innerHeight
  const headerHeight = 100 // Approximate header height
  const padding = 40 // Padding/margins
  
  // Set height to content height, but ensure it's at least viewport height
  const desiredHeight = Math.max(contentHeight, viewportHeight - headerHeight - padding)
  
  node.style.height = 'auto' // Let it size naturally first
  node.style.minHeight = desiredHeight + 'px'
  node.style.overflow = 'visible'
  
  return {
    includeMargin: true,
    style: {},
    postProcess: () => { 
      node.style.height = prev.height
      node.style.minHeight = prev.minHeight
      node.style.overflow = prev.overflow 
    }
  }
}

// Add a computed property for dynamic height
const containerHeight = computed(() => {
  if (!props.familyTree || props.familyTree.length === 0) return 'auto'
  
  // Calculate approximate height based on number of sections and members
  const sections = [
    greatGreatGrandparents.value.length > 0,
    greatGrandparents.value.length > 0,
    grandparents.value.length > 0,
    parents.value.length > 0,
    siblings.value.length > 0,
    children.value.length > 0,
    grandchildren.value.length > 0,
    greatGrandchildren.value.length > 0,
    greatGreatGrandchildren.value.length > 0,
    familyMembers.value.length > 0,
    externalMembers.value.length > 0
  ].filter(Boolean).length
  
  const totalMembers = props.familyTree.length
  const baseHeight = 200 // Base height for header and spacing
  const sectionHeight = sections * 80 // Height per section
  const memberHeight = Math.ceil(totalMembers / 4) * 140 // Approximate height per row of members
  
  return Math.max(baseHeight + sectionHeight + memberHeight, window.innerHeight - 200) + 'px'
})

const props = defineProps({
  person: { type: Object, default: null },
  familyTree: { type: Array, default: () => [] },
  member: { type: Object, default: null }, // ensure this exists
  personRelation: { type: String, default: '' }, // new
  loading: { type: Boolean, default: false },
  id: String, 
  print: Boolean,
  type: String,
});

const personRelationLabel = computed(() => props.personRelation || props.member?.relationship?.name || '');

// Helpers
const displayName = (m) => m?.full_name || [m?.first_name, m?.last_name].filter(Boolean).join(' ') || m?.name || 'Unknown';
const initials = (m) => {
  const name = displayName(m);
  return name
    .split(' ')
    .map((n) => n?.[0])
    .filter(Boolean)
    .slice(0, 2)
    .join('')
    .toUpperCase();
};
const displayNameWithNo = (m) => {
  const name = displayName(m);
  return name;
};

const person = computed(() => props.person || null);
function downloadPdf() {
  console.log(props.id, props.type);
  window.open(route('family.pdf', { id: props.id, type: props.type }), '_blank')
}
// flat list from props.familyTree
const raw = computed(() => (Array.isArray(props.familyTree) ? props.familyTree : []));

// relation groups
const REL = {
  greatGreatGrandparents: ['Great Great Grand Father', 'Great Great Grand Mother'],
  greatGrandparents: ['Great Grand Father', 'Great Grand Mother', 'Great Grandfather-in-Law', 'Great Grandmother-in-Law'],
  grandparents: ['Grand Father', 'Grand Mother', 'Grandfather-in-Law', 'Grandmother-in-Law'],
  parents: ['Father', 'Mother', 'Father-in-Law', 'Mother-in-Law'],
  spouse: ['Husband', 'Wife'],
  siblings: ['Brother', 'Sister', 'Brother-in-Law', 'Sister-in-Law'],
  children: ['Son', 'Daughter', 'Son-in-Law', 'Daughter-in-Law'],
  grandchildren: ['Grandson', 'Granddaughter', 'Grandson-in-Law', 'Granddaughter-in-Law'],
  greatGrandchildren: ['Great Grandson', 'Great Granddaughter', 'Great Grandson-in-Law', 'Great Granddaughter-in-Law'],
  greatGreatGrandchildren: ['Great Great Grandson', 'Great Great Granddaughter', 'Great Great Grandson-in-Law', 'Great Great Granddaughter-in-Law'],
  others: [
    'Uncle',
    'Aunt',
    'Nephew',
    'Niece',
    'Great Nephew',
    'Great Niece',
    'Great Great Nephew',
    'Great Great Niece',
    'Nephew-in-law',
    'Niece-in-law',
    'Uncle-in-Law',
    'Aunt-in-Law',
    'Cousin Brother',
    'Cousin Sister',
    'Cousin Brother-in-Law',
    'Cousin Sister-in-Law',
  ],
};
const wrap = (arr) => arr.map((m) => ({ member: m, relationship: m.relation }));

// sections from relation
const grandparents = computed(() => wrap(raw.value.filter((m) => REL.grandparents.includes(m.relation))));
const greatGrandparents = computed(() => wrap(raw.value.filter((m) => REL.greatGrandparents.includes(m.relation))));
const greatGreatGrandparents = computed(() => wrap(raw.value.filter((m) => REL.greatGreatGrandparents.includes(m.relation))));

const parents = computed(() => wrap(raw.value.filter((m) => REL.parents.includes(m.relation))));
const spouse = computed(() => {
  const s = raw.value.find((m) => REL.spouse.includes(m.relation));
  return s ? { member: s, relationship: s.relation } : null;
});

// Primary couple color - ensure it's unique across all groups
const primaryCoupleColor = computed(() => {
  if (spouse.value) {
    // Check if we already have a color for this couple
    let coupleColor = coupleColorMap.get(person.value?.uid) || coupleColorMap.get(spouse.value.member.uid);

    if (!coupleColor) {
      // Assign new unique couple color
      coupleColor = getUniqueCoupleColor();
      // Store it for both members
      coupleColorMap.set(person.value?.uid, coupleColor);
      coupleColorMap.set(spouse.value.member.uid, coupleColor);
    }

    return coupleColor;
  }
  return null;
});

// Global couple color management across all family sections
const globalCoupleColors = [
  'couple-blue',
  'couple-purple',
  'couple-green',
  'couple-orange',
  'couple-pink',
  'couple-teal',
  'couple-indigo',
  'couple-amber',
];
let globalColorIndex = 0;
const usedCoupleColors = new Set();

// Function to get unique single member color
const getSingleMemberColor = () => {
  const singleColors = ['single-gray', 'single-slate', 'single-zinc', 'single-neutral', 'single-stone', 'single-red', 'single-yellow', 'single-lime'];
  return singleColors[globalColorIndex % singleColors.length];
};

// Function to get unique couple color
const getUniqueCoupleColor = () => {
  let coupleColor = globalCoupleColors[globalColorIndex % globalCoupleColors.length];
  while (usedCoupleColors.has(coupleColor)) {
    globalColorIndex++;
    coupleColor = globalCoupleColors[globalColorIndex % globalCoupleColors.length];
  }
  usedCoupleColors.add(coupleColor);
  return coupleColor;
};

// Store couple colors by member UID to ensure consistency
const coupleColorMap = new Map();

// Spouse sequencing and couple color logic
const sortWithSpousesTogether = (members) => {
  if (!members || members.length === 0) return [];

  const sorted = [...members];

  // Sort members so spouses appear next to each other
  for (let i = 0; i < sorted.length - 1; i++) {
    const current = sorted[i];
    const currentSpouseUid = current.member.spouse_uid || raw.value.find((m) => m.uid === current.member.uid)?.spouse_uid;

    if (currentSpouseUid) {
      // Find the spouse in the remaining array
      const spouseIndex = sorted.findIndex((m, idx) => idx > i && m.member.uid === currentSpouseUid);

      if (spouseIndex !== -1) {
        // Move spouse to position right after current
        const spouse = sorted.splice(spouseIndex, 1)[0];
        sorted.splice(i + 1, 0, spouse);
      }
    }
  }

  // Assign colors to members
  const processedMembers = [];

  for (let i = 0; i < sorted.length; i++) {
    const current = sorted[i];
    const next = sorted[i + 1];

    // Check if current and next are spouses
    const areSpouses =
      next &&
      (current.member.spouse_uid === next.member.uid ||
        next.member.spouse_uid === current.member.uid ||
        raw.value.find((m) => m.uid === current.member.uid)?.spouse_uid === next.member.uid ||
        raw.value.find((m) => m.uid === next.member.uid)?.spouse_uid === current.member.uid);

    if (areSpouses) {
      // Check if we already have a color for this couple
      let coupleColor = coupleColorMap.get(current.member.uid) || coupleColorMap.get(next.member.uid);

      if (!coupleColor) {
        // Assign new unique couple color
        coupleColor = getUniqueCoupleColor();
        // Store it for both members
        coupleColorMap.set(current.member.uid, coupleColor);
        coupleColorMap.set(next.member.uid, coupleColor);
      }

      current.coupleColor = coupleColor;
      next.coupleColor = coupleColor;

      // Skip next member since we've processed it
      i++;
      processedMembers.push(current, next);
    } else {
      // Check if this member is part of a couple that already has a color
      const existingCoupleColor = coupleColorMap.get(current.member.uid);
      if (existingCoupleColor) {
        current.coupleColor = existingCoupleColor;
      } else {
        // Assign single member color
        current.singleMemberColor = getSingleMemberColor();
      }
      processedMembers.push(current);
    }

    globalColorIndex++;
  }

  return processedMembers;
};

const siblings = computed(() => sortWithSpousesTogether(wrap(raw.value.filter((m) => REL.siblings.includes(m.relation)))));
const children = computed(() => sortWithSpousesTogether(wrap(raw.value.filter((m) => REL.children.includes(m.relation)))));
const grandchildren = computed(() => sortWithSpousesTogether(wrap(raw.value.filter((m) => REL.grandchildren.includes(m.relation)))));
const greatGrandchildren = computed(() => sortWithSpousesTogether(wrap(raw.value.filter((m) => REL.greatGrandchildren.includes(m.relation)))));
const greatGreatGrandchildren = computed(() =>
  sortWithSpousesTogether(wrap(raw.value.filter((m) => REL.greatGreatGrandchildren.includes(m.relation)))),
);
const familyMembers = computed(() => sortWithSpousesTogether(wrap(raw.value.filter((m) => REL.others.includes(m.relation)))));

// optional: no separate external list (external are shown with badges within each section)
const externalMembers = computed(() => []); // keep UI consistent, no duplicates

// any data?
const hasAnyMembers = computed(() => {
  return !!(
    parents.value.length ||
    siblings.value.length ||
    children.value.length ||
    grandparents.value.length ||
    greatGrandparents.value.length ||
    greatGreatGrandparents.value.length ||
    grandchildren.value.length ||
    greatGrandchildren.value.length ||
    greatGreatGrandchildren.value.length ||
    familyMembers.value.length ||
    externalMembers.value.length ||
    spouse.value
  );
});

// Navigation
function goBack() {
  router.visit('/member/index');
}
</script>

<style scoped>
/* Make the container more flexible */
#family-root {
  min-height: 100vh;
  height: auto;
  transition: min-height 0.3s ease;
}

/* Ensure sections don't have fixed heights */
section {
  height: auto;
  min-height: fit-content;
}

.grid-autofit {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 14px;
  align-items: stretch;
  height: auto;
  min-height: fit-content;
}

.card-neo {
  position: relative;
  border-radius: 14px;
  height: auto;
  min-height: 120px;
  max-height: none;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition:
    transform 0.18s ease,
    box-shadow 0.18s ease,
    border-color 0.18s ease;
  border: 1px solid rgba(0, 0, 0, 0.06);
  background: linear-gradient(180deg, #ffffff, #fafafb);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.9),
    8px 8px 18px rgba(0, 0, 0, 0.06),
    -8px -8px 18px rgba(255, 255, 255, 0.9);
}

.card-neo::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 14px;
  pointer-events: none;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6);
}

.card-neo:hover {
  transform: translateY(-2px);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.9),
    10px 10px 22px rgba(0, 0, 0, 0.08),
    -10px -10px 22px rgba(255, 255, 255, 0.95);
}

.avatar {
  width: 42px;
  height: 42px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.95rem;
  background: #f3f4f6;
  color: #374151;
}

.avatar-lg {
  width: 48px;
  height: 48px;
  font-size: 1.05rem;
}

.name {
  max-width: 180px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.subtle {
  font-size: 0.72rem;
  color: #6b7280;
}

.badge {
  font-size: 0.72rem;
  padding: 4px 8px;
  border-radius: 9999px;
  font-weight: 600;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
}

/* Tones */
.tone-amber {
  border-color: #fcd34d40;
  background: linear-gradient(180deg, #fffbeb, #ffffff);
}

.tone-blue {
  border-color: #60a5fa40;
  background: linear-gradient(180deg, #eff6ff, #ffffff);
}

.tone-indigo {
  border-color: #818cf840;
  background: linear-gradient(180deg, #eef2ff, #ffffff);
}

.tone-pink {
  border-color: #fb718540;
  background: linear-gradient(180deg, #fdf2f8, #ffffff);
}

.tone-emerald {
  border-color: #34d39940;
  background: linear-gradient(180deg, #ecfdf5, #ffffff);
}

.tone-violet {
  border-color: #a78bfa40;
  background: linear-gradient(180deg, #f5f3ff, #ffffff);
}

.tone-teal {
  border-color: #2dd4bf40;
  background: linear-gradient(180deg, #f0fdfa, #ffffff);
}

.tone-slate {
  border-color: #94a3b840;
  background: linear-gradient(180deg, #f8fafc, #ffffff);
}

.tone-orange {
  border-color: #fb923c40;
  background: linear-gradient(180deg, #fff7ed, #ffffff);
}

/* Badge colors */
.badge-amber {
  background: #fef3c7;
  color: #92400e;
}

.badge-blue {
  background: #dbeafe;
  color: #1d4ed8;
}

.badge-indigo {
  background: #e0e7ff;
  color: #3730a3;
}

.badge-pink {
  background: #fce7f3;
  color: #9d174d;
}

.badge-emerald {
  background: #d1fae5;
  color: #065f46;
}

.badge-violet {
  background: #ede9fe;
  color: #5b21b6;
}

.badge-teal {
  background: #ccfbf1;
  color: #134e4a;
}

.badge-slate {
  background: #e2e8f0;
  color: #334155;
}

.badge-orange {
  background: #ffedd5;
  color: #9a3412;
}

/* Couple Colors - Unique gradients for each couple */
.couple-blue {
  border-color: #3b82f640;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe, #ffffff);
}

.couple-purple {
  border-color: #8b5cf640;
  background: linear-gradient(135deg, #e9d5ff, #c4b5fd, #ffffff);
}

.couple-green {
  border-color: #10b98140;
  background: linear-gradient(135deg, #d1fae5, #a7f3d0, #ffffff);
}

.couple-orange {
  border-color: #f59e0b40;
  background: linear-gradient(135deg, #fed7aa, #fdba74, #ffffff);
}

.couple-pink {
  border-color: #ec489940;
  background: linear-gradient(135deg, #fce7f3, #fbcfe8, #ffffff);
}

.couple-teal {
  border-color: #14b8a640;
  background: linear-gradient(135deg, #ccfbf1, #99f6e4, #ffffff);
}

.couple-indigo {
  border-color: #6366f140;
  background: linear-gradient(135deg, #e0e7ff, #c7d2fe, #ffffff);
}

.couple-amber {
  border-color: #f59e0b40;
  background: linear-gradient(135deg, #fef3c7, #fde68a, #ffffff);
}

/* Single Member Colors - Plain solid colors for unmarried members */
.single-gray {
  border-color: #6b728040;
  background: #f9fafb;
}

.single-slate {
  border-color: #64748b40;
  background: #f8fafc;
}

.single-zinc {
  border-color: #71717a40;
  background: #fafafa;
}

.single-neutral {
  border-color: #73737340;
  background: #fafafa;
}

.single-stone {
  border-color: #78716a40;
  background: #fafaf9;
}

.single-red {
  border-color: #ef444440;
  background: #fef2f2;
}

.single-yellow {
  border-color: #eab30840;
  background: #fefce8;
}

.single-lime {
  border-color: #84cc1640;
  background: #f7fee7;
}
</style>
