import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href?: string;
}

export interface NavItem {
  title: string;
  href: string;
  icon?: LucideIcon;
  isActive?: boolean;
  show?: boolean; // Optional property to control visibility based on permissions
}

export interface SharedData extends PageProps {
  name: string;
  quote: { message: string; author: string };
  auth: Auth;
  ziggy: Config & { location: string };
  sidebarOpen: boolean;
  church_code: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Member {
  id: number;
  community_id?: number | null;
  community_cluster_id?: number | null;
  aadhar?: string | null;
  family_no?: string | null;
  member_no?: string | null;
  registration_year?: string | null;
  church_code?: string | null;
  family_sequence?: number | null;
  member_sequence?: number | null;
  marital_status?: 'Single' | 'Married' | 'Divorced' | 'Widowed';
  birth_family_no?: string | null;
  // relation_member_id?: number | null;
  mother_id?: number | null;
  father_id?: number | null;
  spouse_id?: number | null;
  status: 'Resident' | 'Non-Resident' | 'Dead' | 'Redevelopment Unsettled';
  relationship?: string | null;
  last_name?: string | null;
  first_name?: string;
  middle_name?: string | null;
  date_of_birth?: string | null;
  permanent_add1?: string | null;
  permanent_add2?: string | null;
  permanent_add3?: string | null;
  permanent_town_id?: number | null;
  permanent_city_id?: number | null;
  permanent_pincode?: string | null;
  permanent_state_id?: number | null;
  permanent_country_id?: number | null;
  current_add1?: string | null;
  current_add2?: string | null;
  current_add3?: string | null;
  current_town_id?: number | null;
  current_city_id?: number | null;
  current_pincode?: string | null;
  current_state_id?: number | null;
  current_country_id?: number | null;
  contact_no_1?: string | null;
  contact_no_2?: string | null;
  email?: string | null;
  blood_group_id?: number | null;
  school_name?: string | null;
  gender_id?: number | null;
  status_id?: number | null;
  relationship_id?: number | null;
  parish_id?: number | null;
  college_name?: string | null;
  latest_qualifications?: string | null;
  company_name?: string | null;
  designation_id?: number | null;
  income_range_id?: number | null;
  baptismrecord_id?: number | null;
  baptism_parish?: string | null;
  baptism_parish_id?: number | null;
  confirmation_date?: string | null;
  confirmation_minister?: string | null;
  confirmation_parish?: string | null;
  confirmation_parish_id?: number | null;
  marriagerecord_id?: number | null;
  marriage_parish?: string | null;
  marriage_parish_id?: number | null;
  deathrecord_id?: number | null;
  death_parish?: string | null;
  death_parish_id?: number | null;
  cellsAndAssociations?: Array<{ id: number; name: string }> | null;
  scc_heads?: Array<{ id: number; community?: { id: number; name: string } }> | null;
  ppc_heads?: Array<{ id: number; community?: { id: number; name: string } }> | null;
  cluster_heads?: Array<{ id: number; community?: { id: number; name: string }; cluster?: { id: number; name: string } }> | null;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  spouse_source?: string;
  father_source?: string;
  mother_source?: string;
  notes?: string | null;
}

export interface ExternalMember {
  id: number;
  external_member_no?: string | null;
  first_name: string;
  last_name?: string | null;
  gender_id?: number;
  family_no: string;
  father_id?: number;
  mother_id?: number;
  spouse_id?: number;
  father_source?: string;
  mother_source?: string;
  spouse_source?: string;
  address?: string;
  relationship_id?: number;
  father_data?: {
    id: number;
    name: string;
    type: 'internal' | 'external';
  } | null;
  mother_data?: {
    id: number;
    name: string;
    type: 'internal' | 'external';
  } | null;
  spouse_data?: {
    id: number;
    name: string;
    type: 'internal' | 'external';
  } | null;
}

export interface CommunityCluster {
  id: number;
  name: string;
  community_id: number;
}

export type CommunityClusters = CommunityCluster[];
export type Members = Member[];

export interface Community {
  id: number;
  name: string;
  community_clusters: CommunityCluster;
}

export type Communities = Community[];

export interface IncomeRange {
  id: number; // changed from string to number
  name: string;
}

export type IncomeRanges = IncomeRange[];

export interface SCCHead {
  id: number;
  member_id: number;
  community_id: number;
}
export interface Zone {
  id: number | string;
  name: string;
}
export interface PPCHead {
  id: number;
  member_id: number;
  community_id: number;
}
export interface CommunityFund {
  id: number;
  member_id: number;
  member: Member;
  amount: number;
  fund_date: string;
  description?: string | null;
  created_at: string;
  updated_at: string;
}

export type CommunityFunds = CommunityFund[];

export interface Country {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
}

export type Countries = Country[];

export interface State {
  id: number;
  name: string;
  abbr?: string | null;
  country_id?: number | null;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
}

export type States = State[];

export interface City {
  id: number;
  state_id?: number | null;
  name: string;
  created_at?: string | null;
  updated_at?: string | null;
  deleted_at?: string | null;
}
export type Cities = City[];

export interface Town {
  id: number;
  name: string;
  pincode?: string | null;
  city_id?: number | null;
  created_at?: string | null;
  updated_at?: string | null;
  deleted_at?: string | null;
}

export type Towns = Town[];

export interface Column {
  key: string;
  label: string;
  sortable: boolean;
  filterable?: boolean; // optional
}

export interface BloodGroup {
  id: string; // Assuming id is a string, adjust if necessary
  name: string;
}

export type BloodGroups = BloodGroup[];

// countres
export interface Country {
  id: number;
  name: string;
}

export type Countries = Country[];

export interface State {
  id: number;
  name: string;
  country_id: number; // Foreign key to Country
}

export type States = State[];

export interface Designation {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
}

export type Designations = Designation[];

// Role Permissions Types
export interface Role {
  id: number;
  name: string;
}

export interface ModuleAction {
  id: number;
  name: string;
  slug: string;
  action: string;
  icon?: string;
}

export interface Module {
  id: number;
  name: string;
  slug: string;
  icon?: string;
  actions: ModuleAction[];
}

export type Roles = Role[];
export type Modules = Module[];

export type Permissions = Record<number, Record<number, number>>;
export interface AgeGroup {
  id: number;
  name: string;
  description?: string | null;
  min_age: number;
  max_age: number;
  created_at?: string | null;
  updated_at?: string | null;
  deleted_at?: string | null;
}

export type AgeGroups = AgeGroup[];

export interface Parish {
  id: number;
  deanery: string;
  name: string;
  code?: string | null;
  address?: string | null;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
}

export type Parishes = Parish[];

export interface Relationship {
  id: number;
  name: string;
  description?: string | null;
  created_at?: string | null;
  updated_at?: string | null;
  deleted_at?: string | null;
}
export type Relationships = Relationship[];

export interface CellsAndAssociation {
  id: number;
  name: string;
  created_at?: string | null;
  updated_at?: string | null;
  deleted_at?: string | null;
}

export type CellsAndAssociations = CellsAndAssociation[];

export interface FamilyStats {
  totalMembers: number;
  totalFamilies: number;
  averageMembersPerFamily: number;
}
