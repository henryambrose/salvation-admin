import { usePage } from '@inertiajs/vue3';

export function permissionHelpers() {
  const page = usePage();

  const can = (permission: string) => {
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

    // Check for generic "all" permissions
    if (permission.includes('-')) {
      const [action, resource] = permission.split('-');
      
      // Check if user has generic permission for this action
      if (auth.permissions.includes(`${action} all`)) {
        return true;
      }
    }

    // Check for specific permissions
    return auth.permissions?.includes(permission);
  };

  return {
    can,
  };
}
