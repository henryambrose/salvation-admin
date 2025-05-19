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
    new_olsc_id?: string | null;
    old_sal_id?: string | null;
    aadhar?: string | null;
    family_no?: string | null;
    status: 'Resident' | 'Non-Resident' | 'Dead' | 'Redevelopment Unsettled';
    relationship?: string | null;
    last_name?: string | null;
    first_name?: string;
    middle_name?: string | null;
    date_of_birth?: string | null; // ISO 8601 date string
    permanent_add1?: string | null;
    permanent_add2?: string | null;
    permanent_add3?: string | null;
    permanent_town?: string | null;
    permanent_city?: string | null;
    permanent_pincode?: string | null;
    permanent_state?: string | null;
    permanent_country?: string | null;
    current_add1?: string | null;
    current_add2?: string | null;
    current_add3?: string | null;
    current_town?: string | null;
    current_city?: string | null;
    current_pincode?: string | null;
    current_state?: string | null;
    current_country?: string | null;
    contact_no?: string | null;
    email?: string | null;
    blood_group?: string | null;
    cells_and_association_id?: number | null;
    school_name?: string | null;
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

export interface Community {
    id: number;
    name: string,
    community_clusters: CommunityCluster
}

export type Communities = Community[];


export interface CellsAndAssociation {
    id: number;
    name: string;
}

export type CellsAndAssociations = CellsAndAssociation[];

export interface FamilyIncomeRange {
    id: number; name: string;
}

export type FamilyIncomeRanges = FamilyIncomeRange[];
