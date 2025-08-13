<template>
  <div class="p-4 md:p-6">
    <div v-if="loading" class="text-center py-10 text-gray-500">Loading family tree...</div>

    <div v-else-if="!familyTree || !hasAnyMembers" class="text-center py-16">
      <div class="max-w-sm mx-auto">
        <div class="mb-4">
          <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Family Tree Available</h3>
        <p class="text-gray-500 mb-4">This family doesn't have any members assigned yet.</p>
        <button
          @click="goBack"
          class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            @click="goBack"
            aria-label="Go back"
          >
            <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
              <line x1="9" y1="12" x2="21" y2="12"></line>
            </svg>
            Back
          </button>
          <h2 class="text-xl font-semibold">Family Tree</h2>
        </div>
        <div class="text-sm text-gray-500">
          {{ displayNameWithNo(person) || 'Unknown' }}
        </div>
      </div>
      <section v-if="greatGreatGrandparents.length" class="space-y-3">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Great Great Grandparents</h3>
        <div class="grid-autofit">
          <div v-for="p in greatGreatGrandparents" :key="p.member.id" class="card-neo tone-amber">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
                {{ initials(p.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(p.member) }}</div>
                <div class="text-xs text-gray-500">{{ p.relationship }} </div>
              </div>
            </div>
            <span class="badge badge-amber">{{ p.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="greatGrandparents.length" class="space-y-3">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Great Grandparents</h3>
        <div class="grid-autofit">
          <div v-for="p in greatGrandparents" :key="p.member.id" class="card-neo tone-amber">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
                {{ initials(p.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(p.member) }}</div>
                <div class="text-xs text-gray-500">{{ p.relationship }} </div>
              </div>
            </div>
            <span class="badge badge-amber">{{ p.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="grandparents.length" class="space-y-3">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Grandparents</h3>
        <div class="grid-autofit">
          <div v-for="p in grandparents" :key="p.member.id" class="card-neo tone-amber">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
                {{ initials(p.member) }}
              </div>
              <div>
                <div class="name font-medium">{{ displayNameWithNo(p.member) }}</div>
                <div class="text-xs text-gray-500">{{ p.relationship }} </div>
              </div>
            </div>
            <span class="badge badge-amber">{{ p.member.source }}</span>
          </div>
        </div>
      </section>
      <section v-if="parents.length" class="space-y-3">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Parents</h3>
        <div class="grid-autofit">
          <div v-for="p in parents" :key="p.member.id" class="card-neo tone-blue">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
          Primary — {{ displayNameWithNo(person) || 'Unknown' }} <span v-if="person?.family_no"
            class="text-slate-400">({{ person.family_no }})</span>
        </h3>
        <div class="grid-autofit">
          <div class="card-neo tone-indigo">
            <div class="flex items-center space-x-3">
              <div
                class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-base font-semibold">
                {{ initials(person) }}
              </div>
              <div>
                <div class="name font-semibold">{{ displayNameWithNo(person) }}</div>
                <div class="text-xs text-gray-500">
                  Current Person<span v-if="personRelationLabel"> — {{ personRelationLabel }}</span>
                </div>
              </div>
            </div>
            <span class="badge badge-indigo">{{ person?.source }}</span>
          </div>

          <div v-if="spouse" class="card-neo tone-pink">
            <div class="flex items-center space-x-3">
              <div
                class="w-12 h-12 rounded-full bg-pink-50 text-pink-700 flex items-center justify-center text-base font-semibold">
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
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Siblings</h3>
        <div class="grid-autofit">
          <div v-for="s in siblings" :key="s.member.id" class="card-neo tone-violet">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Children</h3>
        <div class="grid-autofit">
          <div v-for="c in children" :key="c.member.id" class="card-neo tone-emerald">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Grandchildren</h3>
        <div class="grid-autofit">
          <div v-for="gc in grandchildren" :key="gc.member.id" class="card-neo tone-teal">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Great Grandchildren</h3>
        <div class="grid-autofit">
          <div v-for="gc in greatGrandchildren" :key="gc.member.id" class="card-neo tone-teal">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Great Great Grandchildren</h3>
        <div class="grid-autofit">
          <div v-for="gc in greatGreatGrandchildren" :key="gc.member.id" class="card-neo tone-teal">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Other Family Members</h3>
        <div class="grid-autofit">
          <div v-for="fm in familyMembers" :key="fm.member.id" class="card-neo tone-slate">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">External Members</h3>
        <div class="grid-autofit">
          <div v-for="em in externalMembers" :key="em.member.id" class="card-neo tone-orange">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
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
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  person: { type: Object, default: null },
  familyTree: { type: Array, default: () => [] },
  member: { type: Object, default: null },            // ensure this exists
  personRelation: { type: String, default: '' },      // new
  loading: { type: Boolean, default: false }
})

const personRelationLabel = computed(() =>
  props.personRelation || props.member?.relationship?.name || ''
)

// Helpers
const displayName = (m) => m?.full_name || [m?.first_name, m?.last_name].filter(Boolean).join(' ') || m?.name || 'Unknown'
const initials = (m) => {
  const name = displayName(m)
  return name.split(' ').map(n => n?.[0]).filter(Boolean).slice(0, 2).join('').toUpperCase()
}
const displayNameWithNo = (m) => {
  const name = displayName(m)
  return name;

}

const person = computed(() => props.person || null)

// flat list from props.familyTree
const raw = computed(() => Array.isArray(props.familyTree) ? props.familyTree : [])

// relation groups
const REL = {
  greatGreatGrandparents: ['Great Great Grand Father', 'Great Great Grand Mother'],
  greatGrandparents: ['Great Grand Father', 'Great Grand Mother','Great Grandfather-in-Law', 'Great Grandmother-in-Law'],
  grandparents: ['Grand Father', 'Grand Mother','Grandfather-in-Law', 'Grandmother-in-Law' ],
  parents: ['Father', 'Mother', 'Father-in-Law', 'Mother-in-Law'],
  spouse: ['Husband', 'Wife'],
  siblings: ['Brother', 'Sister','Brother-in-Law', 'Sister-in-Law'],
  children: ['Son', 'Daughter','Son-in-Law', 'Daughter-in-Law'],
  grandchildren: ['Grandson', 'Granddaughter','Grandson-in-Law', 'Granddaughter-in-Law'],
  greatGrandchildren: ['Great Grandson', 'Great Granddaughter','Great Grandson-in-Law', 'Great Granddaughter-in-Law'],
  greatGreatGrandchildren: ['Great Great Grandson', 'Great Great Granddaughter','Great Great Grandson-in-Law', 'Great Great Granddaughter-in-Law'],
  others: [
    
    'Uncle', 'Aunt', 
    'Nephew', 'Niece',
    'Great Nephew', 'Great Niece',
    'Great Great Nephew', 'Great Great Niece',
    'Nephew-in-law', 'Niece-in-law',
    'Uncle-in-Law', 'Aunt-in-Law',
    'Cousin Brother', 'Cousin Sister',
    'Cousin Brother-in-Law', 'Cousin Sister-in-Law',

  ]
}
const wrap = (arr) => arr.map(m => ({ member: m, relationship: m.relation }))

// sections from relation
const grandparents = computed(() => wrap(raw.value.filter(m => REL.grandparents.includes(m.relation))))
const greatGrandparents = computed(() => wrap(raw.value.filter(m => REL.greatGrandparents.includes(m.relation))))
const greatGreatGrandparents = computed(() => wrap(raw.value.filter(m => REL.greatGreatGrandparents.includes(m.relation))))

const parents = computed(() => wrap(raw.value.filter(m => REL.parents.includes(m.relation))))
const spouse = computed(() => {
  const s = raw.value.find(m => REL.spouse.includes(m.relation))
  return s ? { member: s, relationship: s.relation } : null
})

const siblings = computed(() => wrap(raw.value.filter(m => REL.siblings.includes(m.relation))))
const children = computed(() => wrap(raw.value.filter(m => REL.children.includes(m.relation))))
const grandchildren = computed(() => wrap(raw.value.filter(m => REL.grandchildren.includes(m.relation))))
const greatGrandchildren = computed(() => wrap(raw.value.filter(m => REL.greatGrandchildren.includes(m.relation))))
const greatGreatGrandchildren = computed(() => wrap(raw.value.filter(m => REL.greatGreatGrandchildren.includes(m.relation))))
const familyMembers = computed(() => wrap(raw.value.filter(m => REL.others.includes(m.relation))))

// optional: no separate external list (external are shown with badges within each section)
const externalMembers = computed(() => []) // keep UI consistent, no duplicates

// any data?
const hasAnyMembers = computed(() => {
  return !!(parents.value.length ||
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
    spouse.value)
})

// Navigation
function goBack() {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    router.visit('/member/index')
  }
}
</script>

<style scoped>
.grid-autofit {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 14px;
  align-items: stretch;
}

.card-neo {
  position: relative;
  border-radius: 14px;
  height: 120px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
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
  font-size: .95rem;
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
  font-size: .72rem;
  color: #6b7280;
}

.badge {
  font-size: .72rem;
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
</style>
