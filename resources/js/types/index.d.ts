import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
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
  marital_status?: 'single' | 'married' | 'divorced' | 'widowed';
  current_family_no?: string | null;
  spouse_member_id?: number | null;
  status: 'Resident' | 'Non-Resident' | 'Dead' | 'Redevelopment Unsettled';
  relationship?: string | null;
  last_name?: string | null;
  first_name?: string;
  middle_name?: string | null;
  date_of_birth?: string | null; // ISO 8601 date string
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
  email?: string | null;
  blood_group_id?: number | null;
  cells_and_association_id?: number | null;
  school_name?: string | null;
  gender_id?: number | null;
  status_id?: number | null;
  relationship_id?: number | null;
  parish_id?: number | null;
  college_name?: string | null;
  latest_qualifications?: string | null;
  company_name?: string | null;
  designation?: string | null;
  family_income_range?: string | null;
  baptism_date?: string | null; // ISO 8601 date string
  baptism_reg_no?: string | null;
  baptism_parish?: string | null;
  confirmation_date?: string | null; // ISO 8601 date string
  confirmation_reg_no?: string | null;
  confirmation_parish?: string | null;
  marriage_date?: string | null; // ISO 8601 date string
  marriage_reg_no?: string | null;
  marriage_parish?: string | null;
  death_date?: string | null; // ISO 8601 date string
  deaths_reg_no?: string | null;
  death_parish?: string | null;
  created_at: string; // ISO 8601 date string
  updated_at: string; // ISO 8601 date string
  deleted_at?: string | null; // ISO 8601 date string for soft deletes
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

export interface CellsAndAssociation {
  id: number;
  name: string;
}

export type CellsAndAssociations = CellsAndAssociation[];

export interface FamilyIncomeRange {
  id: number; // changed from string to number
  name: string;
}

export type FamilyIncomeRanges = FamilyIncomeRange[];

export interface SCCHead {
  id: number;
  member_id: number;
  community_id: number;
}
export interface Zone { id: number|string; name: string; }
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
  fund_date: string; // ISO 8601 date string
  description?: string | null;
  created_at: string; // ISO 8601 date string
  updated_at: string; // ISO 8601 date string
}

export type CommunityFunds = CommunityFund[];

export interface Country {
  id: number;
  name: string;
  created_at: string; // ISO 8601 date string
  updated_at: string; // ISO 8601 date string
  deleted_at?: string | null; // ISO 8601 date string for soft deletes
}

export type Countries = Country[];

export interface State {
  id: number;
  name: string;
  abbr?: string | null;
  country_id?: number | null;
  created_at: string; // ISO 8601 date string
  updated_at: string; // ISO 8601 date string
  deleted_at?: string | null; // ISO 8601 date string for soft deletes
}

export type States = State[];

export interface Column {
  key: string;
  label: string;
  sortable: boolean;
  filterable?: boolean; // optional
}

export interface City {
  id: number;
  name: string;
}
export type Cities = City[];

export interface Town {
  id: number;
  state_id?: number | null;
  name: string;
  pincode?: string | null;
  created_at: string; // ISO 8601 date string
  updated_at: string; // ISO 8601 date string
  deleted_at?: string | null; // ISO 8601 date string for soft deletes
}

export type Towns = Town[];

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

export interface Town {
  id: number;
  name: string;
  state_id: number; // Foreign key to State
}

export type Towns = Town[];

export interface Designation {
  id: number;
  name: string;
  created_at: string; // ISO 8601 date string
  updated_at: string; // ISO 8601 date string
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
  created_at?: string | null; // ISO 8601 date string
  updated_at?: string | null; // ISO 8601 date string
  deleted_at?: string | null; // ISO 8601 date string
}

export type AgeGroups = AgeGroup[];

export interface Parish {
  id: number;
  deanery: string;
  name: string;
  code?: string | null;
  address?: string | null;
  created_at: string; // ISO 8601 date string
  updated_at: string; // ISO 8601 date string
  deleted_at?: string | null; // ISO 8601 date string for soft deletes
}

export type Parishes = Parish[];


export interface Relationship {
  id: number;
  name: string;
  description?: string | null;
  created_at?: string | null; // ISO 8601 date string
  updated_at?: string | null; // ISO 8601 date string
  deleted_at?: string | null; // ISO 8601 date string for soft deletes
}

export type Relationships = Relationship[];

export interface FamilyStats {
  totalMembers: number;
  totalFamilies: number;
  averageMembersPerFamily: number;
}
