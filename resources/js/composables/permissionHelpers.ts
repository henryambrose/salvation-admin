import { usePage } from '@inertiajs/vue3';

export function permissionHelpers() {
  const page = usePage();

  const can = (permission: any) => {
    const auth = page.props.auth as any;
    if (!auth || !auth.permissions) {
      return false;
    }

    const roles = auth.roles || [];
    const isSuper = auth.is_superadmin === true || roles.includes('superadmin') || roles.includes('super admin');
    
    // Superadmin has all permissions
    if (isSuper) {
      return true;
    }

    // For other roles, check specific permissions
    return auth.permissions?.includes(permission);
  };

  return {
    can,
  };
}
