<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/sanctum/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sanctum.csrf-cookie',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'home',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NVNg1zvtGWcFWqKF',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/chat' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'chat.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/chat/send' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'chat.send',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/chat/history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'chat.history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.users',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/assign-role' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.assign-role',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/remove-role' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.remove-role',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/update-user-permissions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.update-user-permissions',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/create-role' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.create-role',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/apply-group' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.apply-group',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles-permissions/update-permissions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.update-permissions',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'roles.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/roles/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/register' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'register',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KZAnc2ZAAKithdw4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::20ryQ0r3dJZ0xzAA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/forgot-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.request',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'password.email',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/reset-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/verify-email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.notice',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email/verification-notification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.send',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/confirm-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.confirm',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WhI40EQC89Cj7Sny',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'logout',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nLUYzA0jHbE2UUZb',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'profile.edit',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'profile.destroy',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.edit',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'password.update',
          ),
          1 => NULL,
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/appearance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appearance',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/data-verification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.data-verification',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/bulk-update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.bulk-update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::88j3CsW02iXkehhM',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'member.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/search-families' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.search-families',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/next-available-numbers' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.next-available-numbers',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/search-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.search-members',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/handle-marriage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.handle-marriage',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/member/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/community/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/community' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'community.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/community/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/zone' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'zone.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'zone.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/zone/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'zone.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/blood-group' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/blood-group/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/income-range' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6466L3XIJJS62xaL',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/income-range/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/income-range/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/scc-head' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YymVN459KGI1c0gw',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/scc-head/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/scc-head/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/scc-head/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ppc-head' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::curdC1OhfIxZkQsD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ppc-head/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ppc-head/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ppc-head/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/country' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'country.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'country.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/country/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'country.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/state' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'state.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'state.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/state/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'state.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/city/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'city.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/city' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'city.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'city.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/city/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'city.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/town' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JUba6DlAM2lBlPNT',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'town.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/town/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'town.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/town/deleted' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'town.deleted',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/town/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'town.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/age-group' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/age-group/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/relationship' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yxrFkEKUoqsINfaY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/relationship/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/relationship/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/relationship/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/designation/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'designation.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/designation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'designation.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'designation.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/designation/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'designation.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/gender' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SGqKFqqxGW2jAziY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'gender.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/gender/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'gender.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/gender/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'gender.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gXScGyfeBUd4AL6q',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'status.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/status/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'status.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/status/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'status.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/cells-and-association' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/cells-and-association/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/cells-and-association-members/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/cells-and-association-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/cells-and-association-members/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/parish' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eM802xP83k8gGSjj',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'parish.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/parish/index' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'parish.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/parish/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'parish.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/parish/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'parish.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'users.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'users.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/users/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'users.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clusters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clusters.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'clusters.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clusters/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clusters.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/community-clusters/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/community-clusters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/community-clusters/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/audit/logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'audit.logs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/audit/logs/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'audit.logs.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/external-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/external-members/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/categories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.categories.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.categories.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/mass-intention-types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intention-types.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intention-types.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/mass-types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-types.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-types.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/payment-methods' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.payment-methods.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.payment-methods.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/annual-contributions/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/annual-contributions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/annual-contributions/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/mass-intentions/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/mass-intentions' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/mass-intentions/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/mass-intentions/search/members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.search-members',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fund/permission-denied' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.permission-denied',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/graves' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.graves.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.graves.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/niches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/niches/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/niches/search-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.search-members',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permanent-graves' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permanent-graves/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permanent-graves/search-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.search-members',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permanent-graves/add-valid-member' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.add-valid-member',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/temporary-graves' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/temporary-graves/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/temporary-graves/search-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.search-members',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/valid-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/valid-members/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/valid-members/search-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.search-members',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/valid-members/search-graves' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.search-graves',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/service-types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/service-types/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permanent-grave-bookings' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-grave-bookings.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-grave-bookings.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permanent-grave-bookings/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-grave-bookings.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permanent-grave-bookings/search-permanent-grave' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-grave-bookings.search-permanent-grave',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/temporary-grave-bookings' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/temporary-grave-bookings/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/temporary-grave-bookings/search-members' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.search-members',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/temporary-grave-bookings/eligible-for-transfer' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.eligible-for-transfer',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/niche-transfers' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/niche-transfers/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/niche-transfers/statistics' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.statistics',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/payments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.payments.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.payments.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/grave-categories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/grave-categories/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/graveyard/permission-denied' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permission-denied',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/catholic-calendar' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EKquyEuaz7ujIIDO',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/permission-denied' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'permission-denied',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-419' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::iUkgDiG3LOySDse2',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-404' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NfSP3uGa9q6a9YDS',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-500' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YxfF9nSdzDPUZX3e',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ktebVoflnS8Oobab',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3ciEUVuAlUCajfHP',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/c(?|hat/answer/([^/]++)(*:31)|o(?|mmunity(?|/([^/]++)(?|(*:64)|/(?|edit(*:79)|restore(*:93))|(*:101))|\\-clusters/([^/]++)(?|/(?|restore(*:143)|edit(*:155))|(*:164)))|untry/([^/]++)(?|/(?|restore(*:202)|edit(*:214))|(*:223)))|ity/([^/]++)(?|(*:248)|/(?|edit(*:264)|restore(*:279))|(*:288))|ells\\-and\\-association(?|/([^/]++)(?|(*:334)|/(?|edit(*:350)|restore(*:365))|(*:374))|\\-members/([^/]++)(?|(*:404)|/(?|edit(*:420)|restore(*:435))|(*:444)))|lusters/([^/]++)(?|(*:473)|/(?|edit(*:489)|restore(*:504))|(*:513)))|/r(?|oles(?|\\-permissions/preview/([^/]++)(*:565)|/([^/]++)(?|(*:585)|/(?|edit(*:601)|users(*:614))|(*:623)))|e(?|set\\-password/([^/]++)(*:659)|lationship/([^/]++)(?|(*:689)|/(?|edit(*:705)|restore(*:720))|(*:729))))|/a(?|pi/(?|ppc\\-community/([^/]++)/members(*:782)|community/([^/]++)/members(*:816))|ge\\-group/([^/]++)(?|(*:846)|/(?|edit(*:862)|restore(*:877))|(*:886))|udit/logs/(?|([^/]++)(*:916)|table/([^/]++)(*:938)|record/([^/]++)/([^/]++)(*:970)|user/([^/]++)(*:991)))|/verify\\-email/([^/]++)/([^/]++)(*:1033)|/member/(?|family\\-(?|details/([^/]++)(*:1080)|members/([^/]++)(*:1105))|church\\-statistics(?:/([^/]++))?(*:1147)|([^/]++)(?|(*:1167)|/(?|edit(*:1184)|restore(*:1200)|details(*:1216)|family\\-tree/([^/]++)(*:1246))|(*:1256)))|/zone/([^/]++)(?|(*:1284)|/(?|edit(*:1301)|restore(*:1317))|(*:1327))|/blood\\-group/([^/]++)(?|(*:1362)|/(?|edit(*:1379)|restore(*:1395))|(*:1405))|/income\\-range/([^/]++)(?|(*:1441)|/(?|edit(*:1458)|restore(*:1474))|(*:1484))|/s(?|cc\\-head/([^/]++)(?|(*:1519)|/(?|edit(*:1536)|restore(*:1552))|(*:1562))|t(?|at(?|e/([^/]++)(?|/(?|restore(*:1605)|edit(*:1618))|(*:1628))|us/([^/]++)(?|(*:1652)|/(?|edit(*:1669)|restore(*:1685))|(*:1695)))|orage/(.*)(*:1716)))|/p(?|pc\\-head/([^/]++)(?|(*:1752)|/(?|edit(*:1769)|restore(*:1785))|(*:1795))|arish/([^/]++)(?|(*:1822)|/(?|edit(*:1839)|restore(*:1855))|(*:1865)))|/town/([^/]++)(?|/(?|restore(*:1904)|edit(*:1917))|(*:1927))|/designation/([^/]++)(?|(*:1961)|/(?|edit(*:1978)|restore(*:1994))|(*:2004))|/g(?|ender/([^/]++)(?|(*:2036)|/(?|edit(*:2053)|restore(*:2069))|(*:2079))|raveyard/(?|grave(?|s/([^/]++)(?|(*:2122)|/(?|restore(*:2142)|force\\-delete(*:2164)))|\\-categories/([^/]++)(?|(*:2199)|/(?|edit(*:2216)|restore(*:2232))|(*:2242)))|niche(?|s/([^/]++)(?|(*:2274)|/(?|edit(*:2291)|restore(*:2307))|(*:2317))|\\-transfers/([^/]++)(?|(*:2350)|/(?|approve(*:2370)|reject(*:2385)|c(?|omplete(*:2405)|ancel(*:2419)))))|p(?|ermanent\\-grave(?|s/([^/]++)(?|(*:2467)|/(?|edit(*:2484)|restore(*:2500))|(*:2510))|\\-bookings/([^/]++)(?|(*:2542)|/cancel(*:2558)|(*:2567)))|ayments/(?|create/([^/]++)/([^/]++)(*:2613)|([^/]++)(?|(*:2633)|/(?|balance(?|(*:2656))|receipt(*:2673)))))|temporary\\-grave(?|s/([^/]++)(?|(*:2718)|/(?|edit(*:2735)|restore(*:2751))|(*:2761))|\\-bookings/([^/]++)(?|(*:2793)|/(?|c(?|onfirm(*:2816)|ancel(*:2830))|request\\-transfer(*:2857))))|valid\\-members/([^/]++)(?|(*:2895)|/(?|edit(*:2912)|restore(*:2928))|(*:2938))|service\\-types/(?|([^/]++)(?|(*:2977)|/(?|edit(*:2994)|restore(*:3010)|toggle\\-active(*:3033))|(*:3043))|service\\-types(*:3067))))|/users/([^/]++)(?|(*:3097)|/(?|edit(*:3114)|restore(*:3130))|(*:3140))|/external\\-member(?|s/(?|([^/]++)(?|(*:3186)|/edit(*:3200)|(*:3209))|search(*:3225)|export(*:3240)|([^/]++)/restore(*:3265))|/family\\-details/([^/]++)(*:3300))|/fund/(?|categories/([^/]++)(?|(*:3341)|/(?|restore(*:3361)|force\\-delete(*:3383)))|mass\\-(?|intention(?|\\-types/([^/]++)(?|(*:3434)|/(?|restore(*:3454)|force\\-delete(*:3476)))|s/([^/]++)(?|(*:3500)|/(?|edit(*:3517)|restore(*:3533)|status(*:3548))|(*:3558)))|types/([^/]++)(?|(*:3586)|/(?|restore(*:3606)|force\\-delete(*:3628))))|p(?|ayment\\-methods/([^/]++)(?|(*:3671)|/(?|restore(*:3691)|force\\-delete(*:3713)))|ending\\-amounts/([^/]++)/([^/]++)(?|(*:3760)|/([^/]++)(*:3778)))|annual\\-contributions/(?|([^/]++)(?|(*:3825)|/edit(*:3839)|(*:3848))|bulk\\-update(*:3870)|([^/]++)/restore(*:3895))|family\\-contributions/([^/]++)(*:3935)))/?$}sDu',
    ),
    3 => 
    array (
      31 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'chat.answer',
          ),
          1 => 
          array (
            0 => 'uniqueID',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      64 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community.show',
          ),
          1 => 
          array (
            0 => 'community',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      79 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community.edit',
          ),
          1 => 
          array (
            0 => 'community',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      93 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      101 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community.update',
          ),
          1 => 
          array (
            0 => 'community',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'community.destroy',
          ),
          1 => 
          array (
            0 => 'community',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      143 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      155 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.edit',
          ),
          1 => 
          array (
            0 => 'community_cluster',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      164 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.show',
          ),
          1 => 
          array (
            0 => 'community_cluster',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.update',
          ),
          1 => 
          array (
            0 => 'community_cluster',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'community-clusters.destroy',
          ),
          1 => 
          array (
            0 => 'community_cluster',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      202 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RN9B5WIPqazj5wFz',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      214 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'country.edit',
          ),
          1 => 
          array (
            0 => 'country',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      223 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'country.show',
          ),
          1 => 
          array (
            0 => 'country',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'country.update',
          ),
          1 => 
          array (
            0 => 'country',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'country.destroy',
          ),
          1 => 
          array (
            0 => 'country',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      248 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'city.show',
          ),
          1 => 
          array (
            0 => 'city',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      264 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'city.edit',
          ),
          1 => 
          array (
            0 => 'city',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      279 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'city.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      288 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'city.update',
          ),
          1 => 
          array (
            0 => 'city',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'city.destroy',
          ),
          1 => 
          array (
            0 => 'city',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      334 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.show',
          ),
          1 => 
          array (
            0 => 'cells_and_association',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      350 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.edit',
          ),
          1 => 
          array (
            0 => 'cells_and_association',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      365 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      374 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.update',
          ),
          1 => 
          array (
            0 => 'cells_and_association',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association.destroy',
          ),
          1 => 
          array (
            0 => 'cells_and_association',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      404 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.show',
          ),
          1 => 
          array (
            0 => 'cells_and_association_member',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      420 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.edit',
          ),
          1 => 
          array (
            0 => 'cells_and_association_member',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      435 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      444 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.update',
          ),
          1 => 
          array (
            0 => 'cells_and_association_member',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'cells-and-association-members.destroy',
          ),
          1 => 
          array (
            0 => 'cells_and_association_member',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      473 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clusters.show',
          ),
          1 => 
          array (
            0 => 'cluster',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      489 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clusters.edit',
          ),
          1 => 
          array (
            0 => 'cluster',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      504 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'cluster.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      513 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clusters.update',
          ),
          1 => 
          array (
            0 => 'cluster',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'clusters.destroy',
          ),
          1 => 
          array (
            0 => 'cluster',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      565 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.permissions.preview',
          ),
          1 => 
          array (
            0 => 'groupId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      585 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.show',
          ),
          1 => 
          array (
            0 => 'role',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      601 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.edit',
          ),
          1 => 
          array (
            0 => 'role',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      614 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.users.get',
          ),
          1 => 
          array (
            0 => 'role',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      623 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'roles.update',
          ),
          1 => 
          array (
            0 => 'role',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'roles.destroy',
          ),
          1 => 
          array (
            0 => 'role',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      659 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.reset',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      689 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.show',
          ),
          1 => 
          array (
            0 => 'relationship',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      705 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.edit',
          ),
          1 => 
          array (
            0 => 'relationship',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      720 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      729 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.update',
          ),
          1 => 
          array (
            0 => 'relationship',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'relationship.destroy',
          ),
          1 => 
          array (
            0 => 'relationship',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      782 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::w9wMsLeWqyEFZM6c',
          ),
          1 => 
          array (
            0 => 'communityId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      816 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WyAMZJ0rcdgL4K3s',
          ),
          1 => 
          array (
            0 => 'communityId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      846 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.show',
          ),
          1 => 
          array (
            0 => 'age_group',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      862 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.edit',
          ),
          1 => 
          array (
            0 => 'age_group',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      877 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      886 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.update',
          ),
          1 => 
          array (
            0 => 'age_group',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'age-group.destroy',
          ),
          1 => 
          array (
            0 => 'age_group',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      916 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'audit.logs.show',
          ),
          1 => 
          array (
            0 => 'auditLog',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      938 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'audit.logs.table',
          ),
          1 => 
          array (
            0 => 'tableName',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      970 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'audit.logs.record',
          ),
          1 => 
          array (
            0 => 'tableName',
            1 => 'recordId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      991 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'audit.logs.user',
          ),
          1 => 
          array (
            0 => 'userId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1033 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.verify',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'hash',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1080 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.family-details',
          ),
          1 => 
          array (
            0 => 'familyNo',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1105 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.family-members',
          ),
          1 => 
          array (
            0 => 'familyNo',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1147 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.church-statistics',
            'churchCode' => NULL,
          ),
          1 => 
          array (
            0 => 'churchCode',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1167 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.show',
          ),
          1 => 
          array (
            0 => 'member',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1184 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.edit',
          ),
          1 => 
          array (
            0 => 'member',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1200 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1216 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.details',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1246 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.family-tree',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1256 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'member.update',
          ),
          1 => 
          array (
            0 => 'member',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'member.destroy',
          ),
          1 => 
          array (
            0 => 'member',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1284 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'zone.show',
          ),
          1 => 
          array (
            0 => 'zone',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1301 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'zone.edit',
          ),
          1 => 
          array (
            0 => 'zone',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1317 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'zone.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1327 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'zone.update',
          ),
          1 => 
          array (
            0 => 'zone',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'zone.destroy',
          ),
          1 => 
          array (
            0 => 'zone',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1362 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.show',
          ),
          1 => 
          array (
            0 => 'blood_group',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1379 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.edit',
          ),
          1 => 
          array (
            0 => 'blood_group',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1395 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1405 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.update',
          ),
          1 => 
          array (
            0 => 'blood_group',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'blood-group.destroy',
          ),
          1 => 
          array (
            0 => 'blood_group',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1441 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.show',
          ),
          1 => 
          array (
            0 => 'income_range',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1458 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.edit',
          ),
          1 => 
          array (
            0 => 'income_range',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1474 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1484 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.update',
          ),
          1 => 
          array (
            0 => 'income_range',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'income-range.destroy',
          ),
          1 => 
          array (
            0 => 'income_range',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1519 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.show',
          ),
          1 => 
          array (
            0 => 'scc_head',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1536 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.edit',
          ),
          1 => 
          array (
            0 => 'scc_head',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1552 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1562 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.update',
          ),
          1 => 
          array (
            0 => 'scc_head',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'scc-head.destroy',
          ),
          1 => 
          array (
            0 => 'scc_head',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1605 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'state.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1618 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'state.edit',
          ),
          1 => 
          array (
            0 => 'state',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1628 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'state.show',
          ),
          1 => 
          array (
            0 => 'state',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'state.update',
          ),
          1 => 
          array (
            0 => 'state',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'state.destroy',
          ),
          1 => 
          array (
            0 => 'state',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1652 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'status.show',
          ),
          1 => 
          array (
            0 => 'status',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1669 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'status.edit',
          ),
          1 => 
          array (
            0 => 'status',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1685 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'status.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1695 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'status.update',
          ),
          1 => 
          array (
            0 => 'status',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'status.destroy',
          ),
          1 => 
          array (
            0 => 'status',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1716 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1752 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.show',
          ),
          1 => 
          array (
            0 => 'ppc_head',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1769 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.edit',
          ),
          1 => 
          array (
            0 => 'ppc_head',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1785 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1795 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.update',
          ),
          1 => 
          array (
            0 => 'ppc_head',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'ppc-head.destroy',
          ),
          1 => 
          array (
            0 => 'ppc_head',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1822 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'parish.show',
          ),
          1 => 
          array (
            0 => 'parish',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1839 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'parish.edit',
          ),
          1 => 
          array (
            0 => 'parish',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1855 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'parish.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1865 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'parish.update',
          ),
          1 => 
          array (
            0 => 'parish',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'parish.destroy',
          ),
          1 => 
          array (
            0 => 'parish',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1904 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'town.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1917 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'town.edit',
          ),
          1 => 
          array (
            0 => 'town',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1927 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'town.show',
          ),
          1 => 
          array (
            0 => 'town',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'town.update',
          ),
          1 => 
          array (
            0 => 'town',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'town.destroy',
          ),
          1 => 
          array (
            0 => 'town',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1961 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'designation.show',
          ),
          1 => 
          array (
            0 => 'designation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1978 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'designation.edit',
          ),
          1 => 
          array (
            0 => 'designation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1994 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'designation.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2004 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'designation.update',
          ),
          1 => 
          array (
            0 => 'designation',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'designation.destroy',
          ),
          1 => 
          array (
            0 => 'designation',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2036 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'gender.show',
          ),
          1 => 
          array (
            0 => 'gender',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2053 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'gender.edit',
          ),
          1 => 
          array (
            0 => 'gender',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2069 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'gender.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2079 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'gender.update',
          ),
          1 => 
          array (
            0 => 'gender',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'gender.destroy',
          ),
          1 => 
          array (
            0 => 'gender',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2122 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.graves.show',
          ),
          1 => 
          array (
            0 => 'grave',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.graves.update',
          ),
          1 => 
          array (
            0 => 'grave',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.graves.destroy',
          ),
          1 => 
          array (
            0 => 'grave',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2142 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.graves.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2164 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.graves.force-delete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2199 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.show',
          ),
          1 => 
          array (
            0 => 'graveCategory',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2216 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.edit',
          ),
          1 => 
          array (
            0 => 'graveCategory',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2232 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2242 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.update',
          ),
          1 => 
          array (
            0 => 'graveCategory',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.grave-categories.destroy',
          ),
          1 => 
          array (
            0 => 'graveCategory',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2274 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.show',
          ),
          1 => 
          array (
            0 => 'niche',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2291 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.edit',
          ),
          1 => 
          array (
            0 => 'niche',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2307 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2317 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.update',
          ),
          1 => 
          array (
            0 => 'niche',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niches.destroy',
          ),
          1 => 
          array (
            0 => 'niche',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2350 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.show',
          ),
          1 => 
          array (
            0 => 'nicheTransfer',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2370 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.approve',
          ),
          1 => 
          array (
            0 => 'nicheTransfer',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2385 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.reject',
          ),
          1 => 
          array (
            0 => 'nicheTransfer',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2405 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.complete',
          ),
          1 => 
          array (
            0 => 'nicheTransfer',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2419 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.niche-transfers.cancel',
          ),
          1 => 
          array (
            0 => 'nicheTransfer',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2467 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.show',
          ),
          1 => 
          array (
            0 => 'permanentGrave',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2484 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.edit',
          ),
          1 => 
          array (
            0 => 'permanentGrave',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2500 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2510 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.update',
          ),
          1 => 
          array (
            0 => 'permanentGrave',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-graves.destroy',
          ),
          1 => 
          array (
            0 => 'permanentGrave',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2542 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-grave-bookings.show',
          ),
          1 => 
          array (
            0 => 'permanentGraveBooking',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2558 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-grave-bookings.cancel',
          ),
          1 => 
          array (
            0 => 'permanentGraveBooking',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2567 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.permanent-grave-bookings.destroy',
          ),
          1 => 
          array (
            0 => 'permanentGraveBooking',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2613 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.payments.create',
          ),
          1 => 
          array (
            0 => 'bookingType',
            1 => 'bookingId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2633 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.payments.show',
          ),
          1 => 
          array (
            0 => 'payment',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2656 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.payments.balance',
          ),
          1 => 
          array (
            0 => 'payment',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.payments.balance.store',
          ),
          1 => 
          array (
            0 => 'payment',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2673 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.payments.receipt',
          ),
          1 => 
          array (
            0 => 'payment',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2718 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.show',
          ),
          1 => 
          array (
            0 => 'temporaryGrave',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2735 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.edit',
          ),
          1 => 
          array (
            0 => 'temporaryGrave',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2751 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2761 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.update',
          ),
          1 => 
          array (
            0 => 'temporaryGrave',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-graves.destroy',
          ),
          1 => 
          array (
            0 => 'temporaryGrave',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2793 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.show',
          ),
          1 => 
          array (
            0 => 'temporaryGraveBooking',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2816 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.confirm',
          ),
          1 => 
          array (
            0 => 'temporaryGraveBooking',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2830 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.cancel',
          ),
          1 => 
          array (
            0 => 'temporaryGraveBooking',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2857 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.temporary-grave-bookings.request-transfer',
          ),
          1 => 
          array (
            0 => 'temporaryGraveBooking',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2895 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.show',
          ),
          1 => 
          array (
            0 => 'validMember',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2912 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.edit',
          ),
          1 => 
          array (
            0 => 'validMember',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2928 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2938 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.update',
          ),
          1 => 
          array (
            0 => 'validMember',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.valid-members.destroy',
          ),
          1 => 
          array (
            0 => 'validMember',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2977 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.show',
          ),
          1 => 
          array (
            0 => 'serviceType',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2994 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.edit',
          ),
          1 => 
          array (
            0 => 'serviceType',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3010 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3033 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.toggle-active',
          ),
          1 => 
          array (
            0 => 'serviceType',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3043 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.update',
          ),
          1 => 
          array (
            0 => 'serviceType',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.destroy',
          ),
          1 => 
          array (
            0 => 'serviceType',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3067 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'graveyard.service-types.api',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3097 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'users.show',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3114 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'users.edit',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3130 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'users.restore',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3140 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'users.update',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'users.destroy',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3186 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.show',
          ),
          1 => 
          array (
            0 => 'external_member',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3200 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.edit',
          ),
          1 => 
          array (
            0 => 'external_member',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3209 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.update',
          ),
          1 => 
          array (
            0 => 'external_member',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.destroy',
          ),
          1 => 
          array (
            0 => 'external_member',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3225 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.search',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3240 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.export',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3265 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-members.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3300 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'external-member.family-details',
          ),
          1 => 
          array (
            0 => 'familyNo',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3341 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.categories.show',
          ),
          1 => 
          array (
            0 => 'category',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.categories.update',
          ),
          1 => 
          array (
            0 => 'category',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'fund.categories.destroy',
          ),
          1 => 
          array (
            0 => 'category',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3361 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.categories.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3383 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.categories.force-delete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3434 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intention-types.show',
          ),
          1 => 
          array (
            0 => 'massIntentionType',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intention-types.update',
          ),
          1 => 
          array (
            0 => 'massIntentionType',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intention-types.destroy',
          ),
          1 => 
          array (
            0 => 'massIntentionType',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3454 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intention-types.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3476 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intention-types.force-delete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3500 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.show',
          ),
          1 => 
          array (
            0 => 'massIntention',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3517 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.edit',
          ),
          1 => 
          array (
            0 => 'massIntention',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3533 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3548 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.update-status',
          ),
          1 => 
          array (
            0 => 'massIntention',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3558 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.update',
          ),
          1 => 
          array (
            0 => 'massIntention',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-intentions.destroy',
          ),
          1 => 
          array (
            0 => 'massIntention',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3586 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-types.show',
          ),
          1 => 
          array (
            0 => 'massType',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-types.update',
          ),
          1 => 
          array (
            0 => 'massType',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-types.destroy',
          ),
          1 => 
          array (
            0 => 'massType',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3606 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-types.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3628 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.mass-types.force-delete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3671 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.payment-methods.show',
          ),
          1 => 
          array (
            0 => 'paymentMethod',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.payment-methods.update',
          ),
          1 => 
          array (
            0 => 'paymentMethod',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'fund.payment-methods.destroy',
          ),
          1 => 
          array (
            0 => 'paymentMethod',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3691 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.payment-methods.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3713 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.payment-methods.force-delete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3760 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.pending-amounts',
          ),
          1 => 
          array (
            0 => 'familyNo',
            1 => 'year',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3778 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.pending-amounts-by-category',
          ),
          1 => 
          array (
            0 => 'familyNo',
            1 => 'year',
            2 => 'categoryId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3825 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.show',
          ),
          1 => 
          array (
            0 => 'annual_contribution',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3839 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.edit',
          ),
          1 => 
          array (
            0 => 'annual_contribution',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3848 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.update',
          ),
          1 => 
          array (
            0 => 'annual_contribution',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.destroy',
          ),
          1 => 
          array (
            0 => 'annual_contribution',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3870 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.bulk-update',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3895 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.annual-contributions.restore',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3935 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'fund.family-contributions',
          ),
          1 => 
          array (
            0 => 'familyNo',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'sanctum.csrf-cookie' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sanctum/csrf-cookie',
      'action' => 
      array (
        'uses' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'controller' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'namespace' => NULL,
        'prefix' => 'sanctum',
        'where' => 
        array (
        ),
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'sanctum.csrf-cookie',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'home' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:59:"function () {
    return \\redirect()->route(\'dashboard\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009de0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'home',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NVNg1zvtGWcFWqKF' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'csrf-cookie',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:89:"function () {
    return \\response()->json([\'message\' => \'CSRF token refreshed\'], 200);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009e20000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::NVNg1zvtGWcFWqKF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\DashboardController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DashboardController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles-permissions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'role:super admin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles-permissions/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'role:super admin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'chat.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'chat',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'role:super admin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ChatController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ChatController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'chat.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'chat.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'chat/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'role:super admin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ChatController@sendMessage',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ChatController@sendMessage',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'chat.send',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'chat.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'chat/history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'role:super admin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ChatController@getHistory',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ChatController@getHistory',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'chat.history',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'chat.answer' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'chat/answer/{uniqueID}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'role:super admin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ChatController@getSpecificAnswer',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ChatController@getSpecificAnswer',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'chat.answer',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.users' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles-permissions/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@users',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@users',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.users',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.assign-role' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles-permissions/assign-role',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@assignRole',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@assignRole',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.assign-role',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.remove-role' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles-permissions/remove-role',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@removeRole',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@removeRole',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.remove-role',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.update-user-permissions' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles-permissions/update-user-permissions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@updateUserPermissions',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@updateUserPermissions',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.update-user-permissions',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.create-role' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles-permissions/create-role',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@createRole',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@createRole',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.create-role',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.apply-group' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles-permissions/apply-group',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@applyGroup',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@applyGroup',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.apply-group',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.update-permissions' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles-permissions/update-permissions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@updatePermissions',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@updatePermissions',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.update-permissions',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.permissions.preview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles-permissions/preview/{groupId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@preview',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RolePermissionController@preview',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.permissions.preview',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'as' => 'roles.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'as' => 'roles.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'roles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'as' => 'roles.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles/{role}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'as' => 'roles.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles/{role}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'as' => 'roles.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'roles/{role}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'as' => 'roles.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'roles/{role}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'as' => 'roles.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'roles.users.get' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'roles/{role}/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RoleController@getUsers',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RoleController@getUsers',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'roles.users.get',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::w9wMsLeWqyEFZM6c' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/ppc-community/{communityId}/members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@membersByCommunity',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@membersByCommunity',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::w9wMsLeWqyEFZM6c',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WyAMZJ0rcdgL4K3s' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/community/{communityId}/members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
          4 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@membersByCommunity',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@membersByCommunity',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WyAMZJ0rcdgL4K3s',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'register' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'register',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\RegisteredUserController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\RegisteredUserController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'register',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KZAnc2ZAAKithdw4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'register',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\RegisteredUserController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\RegisteredUserController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::KZAnc2ZAAKithdw4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\AuthenticatedSessionController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\AuthenticatedSessionController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::20ryQ0r3dJZ0xzAA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\AuthenticatedSessionController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\AuthenticatedSessionController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::20ryQ0r3dJZ0xzAA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.request' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'forgot-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\PasswordResetLinkController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\PasswordResetLinkController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.request',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.email' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'forgot-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\PasswordResetLinkController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\PasswordResetLinkController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.email',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.reset' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'reset-password/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\NewPasswordController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\NewPasswordController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.reset',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'reset-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\NewPasswordController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\NewPasswordController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verification.notice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'verify-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\EmailVerificationPromptController@__invoke',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\EmailVerificationPromptController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.notice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verification.verify' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'verify-email/{id}/{hash}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'signed',
          3 => 'throttle:6,1',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\VerifyEmailController@__invoke',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\VerifyEmailController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.verify',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verification.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'email/verification-notification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'throttle:6,1',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\EmailVerificationNotificationController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\EmailVerificationNotificationController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.send',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.confirm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'confirm-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\ConfirmablePasswordController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\ConfirmablePasswordController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.confirm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WhI40EQC89Cj7Sny' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'confirm-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\ConfirmablePasswordController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\ConfirmablePasswordController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WhI40EQC89Cj7Sny',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'logout' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Auth\\AuthenticatedSessionController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Auth\\AuthenticatedSessionController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nLUYzA0jHbE2UUZb' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'settings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::nLUYzA0jHbE2UUZb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/settings/profile',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'profile.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Settings\\ProfileController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Settings\\ProfileController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'profile.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'settings/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Settings\\ProfileController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Settings\\ProfileController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'profile.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'profile.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'settings/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Settings\\ProfileController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Settings\\ProfileController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'profile.destroy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Settings\\PasswordController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Settings\\PasswordController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'settings/password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\Settings\\PasswordController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\Settings\\PasswordController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appearance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/appearance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:83:"function () {
        return \\Inertia\\Inertia::render(\'settings/Appearance\');
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000008460000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'appearance',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.data-verification' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/data-verification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
          3 => 'permission:read-data-verification',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@dataVerification',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@dataVerification',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.data-verification',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.bulk-update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'member/bulk-update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
          3 => 'permission:update-data-verification',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@bulkUpdate',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@bulkUpdate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.bulk-update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::88j3CsW02iXkehhM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'member',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::88j3CsW02iXkehhM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/member/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.search-families' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/search-families',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@searchFamilies',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@searchFamilies',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.search-families',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.next-available-numbers' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/next-available-numbers',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@getNextAvailableNumbers',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@getNextAvailableNumbers',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.next-available-numbers',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.family-details' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/family-details/{familyNo}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@getFamilyDetails',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@getFamilyDetails',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.family-details',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.family-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/family-members/{familyNo}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@getMembersByFamily',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@getMembersByFamily',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.family-members',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.church-statistics' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/church-statistics/{churchCode?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@getChurchStatistics',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@getChurchStatistics',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.church-statistics',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.search-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/search-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@searchMembers',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@searchMembers',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.search-members',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.handle-marriage' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'member/handle-marriage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@handleMarriage',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@handleMarriage',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.handle-marriage',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'as' => 'member.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'member',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'as' => 'member.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/{member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'as' => 'member.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/{member}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'as' => 'member.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'member/{member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'as' => 'member.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'member/{member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'as' => 'member.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'member/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.details' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/{id}/details',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
          2 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@getMemberDetails',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@getMemberDetails',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.details',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'member.family-tree' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'member/{id}/family-tree/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\MemberController@showFamilyTree',
        'controller' => 'Modules\\Members\\Http\\Controllers\\MemberController@showFamilyTree',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'member.family-tree',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'community.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'community',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community/{community}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community/{community}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'community/{community}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'community/{community}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'community/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'community.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'zone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'zone.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'zone/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'zone.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'zone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'zone.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'zone/{zone}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'zone.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'zone/{zone}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'zone.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'zone/{zone}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'zone.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'zone/{zone}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'zone.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'zone.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'zone/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ZoneController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ZoneController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'zone.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'blood-group',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'blood-group.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'blood-group/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'blood-group.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'blood-group',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'blood-group.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'blood-group/{blood_group}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'blood-group.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'blood-group/{blood_group}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'blood-group.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'blood-group/{blood_group}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'blood-group.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'blood-group/{blood_group}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'blood-group.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'blood-group.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'blood-group/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\BloodGroupController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'blood-group.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6466L3XIJJS62xaL' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'income-range',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::6466L3XIJJS62xaL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/income-range/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'income-range/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'income-range.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'income-range/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'income-range.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'income-range',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'income-range.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'income-range/{income_range}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'income-range.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'income-range/{income_range}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'income-range.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'income-range/{income_range}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'income-range.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'income-range/{income_range}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'income-range.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'income-range.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'income-range/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\IncomeRangeController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'income-range.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YymVN459KGI1c0gw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'scc-head',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::YymVN459KGI1c0gw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/scc-head/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'scc-head/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'scc-head.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'scc-head/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'scc-head.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'scc-head/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'scc-head.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'scc-head',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'scc-head.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'scc-head/{scc_head}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'scc-head.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'scc-head/{scc_head}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'scc-head.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'scc-head/{scc_head}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'scc-head.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'scc-head/{scc_head}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'scc-head.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'scc-head.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'scc-head/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\SCCHeadController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'scc-head.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::curdC1OhfIxZkQsD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'ppc-head',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::curdC1OhfIxZkQsD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/ppc-head/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ppc-head/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'ppc-head.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ppc-head/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'ppc-head.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ppc-head/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'ppc-head.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'ppc-head',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'ppc-head.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ppc-head/{ppc_head}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'ppc-head.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ppc-head/{ppc_head}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'ppc-head.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'ppc-head/{ppc_head}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'ppc-head.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'ppc-head/{ppc_head}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'ppc-head.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ppc-head.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'ppc-head/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\PPCHeadController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'ppc-head.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RN9B5WIPqazj5wFz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'country/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::RN9B5WIPqazj5wFz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'country.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'country',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'country.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'country.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'country/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'country.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'country.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'country',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'country.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'country.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'country/{country}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'country.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'country.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'country/{country}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'country.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'country.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'country/{country}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'country.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'country.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'country/{country}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'country.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CountryController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CountryController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'state/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'state.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'state',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'state.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'state/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'state.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'state',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'state.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'state/{state}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'state.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'state/{state}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'state.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'state/{state}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'state.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'state.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'state/{state}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'state.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StateController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StateController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'city/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'city.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'city',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'city.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'city/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'city.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'city',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'city.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'city/{city}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'city.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'city/{city}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'city.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'city/{city}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'city.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'city/{city}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'city.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'city.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'city/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CityController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CityController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'city.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'town/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'town.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JUba6DlAM2lBlPNT' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'town',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::JUba6DlAM2lBlPNT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/town/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'town/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'town.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.deleted' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'town/deleted',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@deleted',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@deleted',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'town.deleted',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'town/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'town.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'town',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'town.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'town/{town}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'town.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'town/{town}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'town.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'town/{town}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'town.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'town.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'town/{town}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'town.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\TownController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\TownController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'age-group',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'age-group.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'age-group/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'age-group.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'age-group',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'age-group.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'age-group/{age_group}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'age-group.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'age-group/{age_group}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'age-group.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'age-group/{age_group}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'age-group.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'age-group/{age_group}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'age-group.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'age-group.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'age-group/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AgeGroupController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'age-group.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yxrFkEKUoqsINfaY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'relationship',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::yxrFkEKUoqsINfaY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/relationship/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'relationship/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'relationship.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'relationship/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'relationship.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'relationship/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'relationship.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'relationship',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'relationship.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'relationship/{relationship}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'relationship.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'relationship/{relationship}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'relationship.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'relationship/{relationship}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'relationship.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'relationship/{relationship}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'relationship.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'relationship.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'relationship/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\RelationshipController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'relationship.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'designation/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'designation.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'designation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'designation.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'designation/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'designation.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'designation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'designation.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'designation/{designation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'designation.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'designation/{designation}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'designation.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'designation/{designation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'designation.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'designation/{designation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'designation.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'designation.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'designation/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\DesignationController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\DesignationController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'designation.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SGqKFqqxGW2jAziY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'gender',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::SGqKFqqxGW2jAziY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/gender/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'gender/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'gender.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'gender/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'gender.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'gender',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'gender.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'gender/{gender}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'gender.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'gender/{gender}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'gender.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'gender/{gender}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'gender.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'gender/{gender}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'gender.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'gender.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'gender/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\GenderController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\GenderController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'gender.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gXScGyfeBUd4AL6q' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::gXScGyfeBUd4AL6q',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/status/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'status/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'status.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'status/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'status.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'status.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'status/{status}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'status.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'status/{status}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'status.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'status/{status}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'status.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'status/{status}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'status.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'status.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'status/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\StatusController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\StatusController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'status.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'cells-and-association',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association/{cells_and_association}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association/{cells_and_association}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'cells-and-association/{cells_and_association}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'cells-and-association/{cells_and_association}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'cells-and-association/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'cells-and-association.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association-members/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'cells-and-association-members.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association-members.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association-members/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association-members.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'cells-and-association-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association-members.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association-members/{cells_and_association_member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association-members.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cells-and-association-members/{cells_and_association_member}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association-members.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'cells-and-association-members/{cells_and_association_member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association-members.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'cells-and-association-members/{cells_and_association_member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'cells-and-association-members.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cells-and-association-members.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'cells-and-association-members/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CellsAndAssociationMemberController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'cells-and-association-members.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eM802xP83k8gGSjj' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'parish',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::eM802xP83k8gGSjj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/parish/index',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'parish/index',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'parish.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'parish/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'parish.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'parish/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'parish.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'parish',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'parish.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'parish/{parish}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'parish.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'parish/{parish}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'parish.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'parish/{parish}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'parish.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'parish/{parish}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'parish.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'parish.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'parish/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ParishController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ParishController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'parish.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'users.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'users/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'users.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'users.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'users/{user}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'users.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'users/{user}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'users.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'users/{user}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'users.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'users/{user}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'users.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'users.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'users/{user}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\UserController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\UserController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'users.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clusters.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clusters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'clusters.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clusters.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clusters/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'clusters.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clusters.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clusters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'clusters.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clusters.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clusters/{cluster}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'clusters.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clusters.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clusters/{cluster}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'clusters.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clusters.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'clusters/{cluster}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'clusters.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clusters.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'clusters/{cluster}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'clusters.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'cluster.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clusters/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ClusterController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ClusterController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'cluster.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community-clusters/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'community-clusters.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'community-clusters/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'community-clusters.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community-clusters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community-clusters.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community-clusters/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community-clusters.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'community-clusters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community-clusters.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community-clusters/{community_cluster}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community-clusters.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'community-clusters/{community_cluster}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community-clusters.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'community-clusters/{community_cluster}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community-clusters.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'community-clusters.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'community-clusters/{community_cluster}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'community-clusters.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CommunityClusterController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'audit.logs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit/logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'superadmin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@index',
        'as' => 'audit.logs.index',
        'namespace' => NULL,
        'prefix' => '/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'audit.logs.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit/logs/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'superadmin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@export',
        'as' => 'audit.logs.export',
        'namespace' => NULL,
        'prefix' => '/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'audit.logs.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit/logs/{auditLog}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'superadmin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@show',
        'as' => 'audit.logs.show',
        'namespace' => NULL,
        'prefix' => '/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'audit.logs.table' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit/logs/table/{tableName}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'superadmin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@table',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@table',
        'as' => 'audit.logs.table',
        'namespace' => NULL,
        'prefix' => '/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'audit.logs.record' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit/logs/record/{tableName}/{recordId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'superadmin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@record',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@record',
        'as' => 'audit.logs.record',
        'namespace' => NULL,
        'prefix' => '/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'audit.logs.user' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit/logs/user/{userId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'superadmin',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@user',
        'controller' => 'Modules\\Members\\Http\\Controllers\\AuditLogController@user',
        'as' => 'audit.logs.user',
        'namespace' => NULL,
        'prefix' => '/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'external-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'as' => 'external-members.index',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'external-members/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'as' => 'external-members.create',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@create',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'external-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'as' => 'external-members.store',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@store',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'external-members/{external_member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'as' => 'external-members.show',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@show',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'external-members/{external_member}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'as' => 'external-members.edit',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@edit',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@edit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'external-members/{external_member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'as' => 'external-members.update',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@update',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@update',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'external-members/{external_member}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'as' => 'external-members.destroy',
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@destroy',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.search' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'external-members/search',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@search',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@search',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'external-members.search',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-member.family-details' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'external-member/family-details/{familyNo}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@getFamilyDetails',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@getFamilyDetails',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'external-member.family-details',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'external-members/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@export',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@export',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'external-members.export',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'external-members.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'external-members/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@restore',
        'controller' => 'Modules\\Members\\Http\\Controllers\\ExternalMemberController@restore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'external-members.restore',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:61:"function () {
        return \\inertia(\'FundDashboard\');
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009140000000000000000";}}',
        'as' => 'fund.dashboard',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.categories.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@index',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@index',
        'as' => 'fund.categories.index',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.categories.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@store',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@store',
        'as' => 'fund.categories.store',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.categories.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/categories/{category}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@show',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@show',
        'as' => 'fund.categories.show',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.categories.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'fund/categories/{category}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@update',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@update',
        'as' => 'fund.categories.update',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.categories.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/categories/{category}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@destroy',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@destroy',
        'as' => 'fund.categories.destroy',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.categories.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/categories/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@restore',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@restore',
        'as' => 'fund.categories.restore',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.categories.force-delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/categories/{id}/force-delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@forceDelete',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\FundCategoryController@forceDelete',
        'as' => 'fund.categories.force-delete',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intention-types.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intention-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@index',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@index',
        'as' => 'fund.mass-intention-types.index',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intention-types.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/mass-intention-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@store',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@store',
        'as' => 'fund.mass-intention-types.store',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intention-types.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intention-types/{massIntentionType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@show',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@show',
        'as' => 'fund.mass-intention-types.show',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intention-types.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'fund/mass-intention-types/{massIntentionType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@update',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@update',
        'as' => 'fund.mass-intention-types.update',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intention-types.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/mass-intention-types/{massIntentionType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@destroy',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@destroy',
        'as' => 'fund.mass-intention-types.destroy',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intention-types.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/mass-intention-types/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@restore',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@restore',
        'as' => 'fund.mass-intention-types.restore',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intention-types.force-delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/mass-intention-types/{id}/force-delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@forceDelete',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionTypeController@forceDelete',
        'as' => 'fund.mass-intention-types.force-delete',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-types.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@index',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@index',
        'as' => 'fund.mass-types.index',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-types.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/mass-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@store',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@store',
        'as' => 'fund.mass-types.store',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-types.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-types/{massType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@show',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@show',
        'as' => 'fund.mass-types.show',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-types.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'fund/mass-types/{massType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@update',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@update',
        'as' => 'fund.mass-types.update',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-types.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/mass-types/{massType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@destroy',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@destroy',
        'as' => 'fund.mass-types.destroy',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-types.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/mass-types/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@restore',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@restore',
        'as' => 'fund.mass-types.restore',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-types.force-delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/mass-types/{id}/force-delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@forceDelete',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassTypeController@forceDelete',
        'as' => 'fund.mass-types.force-delete',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.payment-methods.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/payment-methods',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@index',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@index',
        'as' => 'fund.payment-methods.index',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.payment-methods.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/payment-methods',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@store',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@store',
        'as' => 'fund.payment-methods.store',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.payment-methods.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/payment-methods/{paymentMethod}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@show',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@show',
        'as' => 'fund.payment-methods.show',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.payment-methods.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'fund/payment-methods/{paymentMethod}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@update',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@update',
        'as' => 'fund.payment-methods.update',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.payment-methods.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/payment-methods/{paymentMethod}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@destroy',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@destroy',
        'as' => 'fund.payment-methods.destroy',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.payment-methods.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/payment-methods/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@restore',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@restore',
        'as' => 'fund.payment-methods.restore',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.payment-methods.force-delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/payment-methods/{id}/force-delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@forceDelete',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\PaymentMethodController@forceDelete',
        'as' => 'fund.payment-methods.force-delete',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/annual-contributions/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@export',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@export',
        'as' => 'fund.annual-contributions.export',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/annual-contributions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'fund.annual-contributions.index',
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@index',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@index',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/annual-contributions/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'fund.annual-contributions.create',
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@create',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@create',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/annual-contributions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'fund.annual-contributions.store',
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@store',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@store',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/annual-contributions/{annual_contribution}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'fund.annual-contributions.show',
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@show',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@show',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/annual-contributions/{annual_contribution}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'fund.annual-contributions.edit',
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@edit',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@edit',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'fund/annual-contributions/{annual_contribution}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'fund.annual-contributions.update',
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@update',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@update',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/annual-contributions/{annual_contribution}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'as' => 'fund.annual-contributions.destroy',
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@destroy',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@destroy',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.bulk-update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/annual-contributions/bulk-update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@bulkUpdate',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@bulkUpdate',
        'as' => 'fund.annual-contributions.bulk-update',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.annual-contributions.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/annual-contributions/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@restore',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\AnnualContributionController@restore',
        'as' => 'fund.annual-contributions.restore',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intentions/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@export',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@export',
        'as' => 'fund.mass-intentions.export',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intentions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@index',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@index',
        'as' => 'fund.mass-intentions.index',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intentions/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@create',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@create',
        'as' => 'fund.mass-intentions.create',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/mass-intentions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@store',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@store',
        'as' => 'fund.mass-intentions.store',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intentions/{massIntention}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@show',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@show',
        'as' => 'fund.mass-intentions.show',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intentions/{massIntention}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@edit',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@edit',
        'as' => 'fund.mass-intentions.edit',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'fund/mass-intentions/{massIntention}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@update',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@update',
        'as' => 'fund.mass-intentions.update',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'fund/mass-intentions/{massIntention}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@destroy',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@destroy',
        'as' => 'fund.mass-intentions.destroy',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'fund/mass-intentions/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@restore',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@restore',
        'as' => 'fund.mass-intentions.restore',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.update-status' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'fund/mass-intentions/{massIntention}/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@updateStatus',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@updateStatus',
        'as' => 'fund.mass-intentions.update-status',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.mass-intentions.search-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/mass-intentions/search/members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@searchMembers',
        'controller' => 'Modules\\Fund\\Http\\Controllers\\MassIntentionController@searchMembers',
        'as' => 'fund.mass-intentions.search-members',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.permission-denied' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/permission-denied',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:200:"function () {
    return \\Inertia\\Inertia::render(\'errors/PermissionDenied\', [
        \'message\' => \\request()->get(\'message\', \'\'),
        \'user\' => \\Illuminate\\Support\\Facades\\Auth::user()
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000008180000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'fund.permission-denied',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.family-contributions' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/family-contributions/{familyNo}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:1746:"function ($familyNo) {
        $contributions = \\Modules\\Fund\\Models\\FamilyContribution::with([\'fundCategory\', \'paymentMethod\', \'member\'])
            ->where(\'family_no\', $familyNo)
            ->orderBy(\'start_date\', \'asc\')
            ->orderBy(\'end_date\', \'asc\')
            ->get()
            ->map(function ($contribution) {
                $paidBy = $contribution->member_id && $contribution->member
                    ? \\trim(($contribution->member->first_name ?? \'\') . \' \' . ($contribution->member->last_name ?? \'\'))
                    : ($contribution->paid_by_name ?? \'\');
                return [
                    \'id\' => $contribution->id,
                    \'start_date\' => $contribution->start_date,
                    \'end_date\' => $contribution->end_date,
                    \'amount\' => $contribution->amount,
                    \'status\' => $contribution->status,
                    \'category_name\' => $contribution->fundCategory->name ?? \'N/A\',
                    \'payment_method\' => $contribution->paymentMethod->name ?? \'N/A\',
                    \'paid_by\' => $paidBy,
                    \'date_of_payment\' => $contribution->created_at,
                    \'notes\' => $contribution->notes
                ];
            });
        
        $totalContributions = $contributions->count();
        $totalPaid = $contributions->where(\'status\', \'paid\')->sum(\'amount\');
        $totalPending = $contributions->whereIn(\'status\', [\'pending\', \'partial\'])->sum(\'amount\');
        
        return \\response()->json([
            \'contributions\' => $contributions,
            \'total_contributions\' => $totalContributions,
            \'total_paid\' => $totalPaid,
            \'total_pending\' => $totalPending
        ]);
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000008910000000000000000";}}',
        'as' => 'fund.family-contributions',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.pending-amounts' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/pending-amounts/{familyNo}/{year}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:1548:"function ($familyNo, $year) {
        // Get all fund categories
        $categories = \\Modules\\Fund\\Models\\FundCategory::all();
        
        $pendingAmounts = $categories->map(function ($category) use ($familyNo, $year) {
            // Get contributions for this category and year
            $contributions = \\Modules\\Fund\\Models\\FamilyContribution::where(\'family_no\', $familyNo)
                ->where(\'year\', $year)
                ->where(\'fund_category_id\', $category->id)
                ->get();
            
            $paidAmount = $contributions->where(\'status\', \'paid\')->sum(\'amount\');
            $partialAmount = $contributions->where(\'status\', \'partial\')->sum(\'amount\');
            $pendingAmount = $contributions->where(\'status\', \'pending\')->sum(\'amount\');
            
            $totalPaid = $paidAmount + $partialAmount;
            
            return [
                \'id\' => $category->id,
                \'name\' => $category->name,
                \'year\' => $year,
                \'paid_amount\' => $totalPaid,
                \'pending_amount\' => $pendingAmount,
                \'contributions\' => $contributions->count()
            ];
        })->filter(function ($category) {
            return $category[\'pending_amount\'] > 0 || $category[\'contributions\'] > 0;
        })->values();
        
        $totalPending = $pendingAmounts->sum(\'pending_amount\');
        
        return \\response()->json([
            \'pending_amounts\' => $pendingAmounts,
            \'total_pending\' => $totalPending
        ]);
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000095d0000000000000000";}}',
        'as' => 'fund.pending-amounts',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'fund.pending-amounts-by-category' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fund/pending-amounts/{familyNo}/{year}/{categoryId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:1278:"function ($familyNo, $year, $categoryId) {
        $category = \\Modules\\Fund\\Models\\FundCategory::find($categoryId);
        if (!$category) {
            return \\response()->json([\'pending_amounts\' => [], \'total_pending\' => 0]);
        }
        
        $contributions = \\Modules\\Fund\\Models\\FamilyContribution::where(\'family_no\', $familyNo)
            ->where(\'year\', $year)
            ->where(\'fund_category_id\', $categoryId)
            ->get();
        
        $paidAmount = $contributions->where(\'status\', \'paid\')->sum(\'amount\');
        $partialAmount = $contributions->where(\'status\', \'partial\')->sum(\'amount\');
        $pendingAmount = $contributions->where(\'status\', \'pending\')->sum(\'amount\');
        
        $totalPaid = $paidAmount + $partialAmount;
        
        $pendingAmounts = [
            [
                \'id\' => $category->id,
                \'name\' => $category->name,
                \'year\' => $year,
                \'paid_amount\' => $totalPaid,
                \'pending_amount\' => $pendingAmount,
                \'contributions\' => $contributions->count()
            ]
        ];
        
        $totalPending = $pendingAmount;
        return \\response()->json([\'pending_amounts\' => $pendingAmounts, \'total_pending\' => $totalPending]);
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009ca0000000000000000";}}',
        'as' => 'fund.pending-amounts-by-category',
        'namespace' => NULL,
        'prefix' => '/fund',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\DashboardController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\DashboardController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'graveyard.dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.graves.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/graves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@index',
        'as' => 'graveyard.graves.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.graves.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/graves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@store',
        'as' => 'graveyard.graves.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.graves.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/graves/{grave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@show',
        'as' => 'graveyard.graves.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.graves.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'graveyard/graves/{grave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@update',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@update',
        'as' => 'graveyard.graves.update',
        'namespace' => NULL,
        'prefix' => '/graveyard/graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.graves.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/graves/{grave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@destroy',
        'as' => 'graveyard.graves.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.graves.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/graves/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@restore',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@restore',
        'as' => 'graveyard.graves.restore',
        'namespace' => NULL,
        'prefix' => '/graveyard/graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.graves.force-delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/graves/{id}/force-delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@forceDelete',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveController@forceDelete',
        'as' => 'graveyard.graves.force-delete',
        'namespace' => NULL,
        'prefix' => '/graveyard/graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niches',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@index',
        'as' => 'graveyard.niches.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niches/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@create',
        'as' => 'graveyard.niches.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/niches',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@store',
        'as' => 'graveyard.niches.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.search-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niches/search-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@searchMembers',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@searchMembers',
        'as' => 'graveyard.niches.search-members',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niches/{niche}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@show',
        'as' => 'graveyard.niches.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niches/{niche}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@edit',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@edit',
        'as' => 'graveyard.niches.edit',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'graveyard/niches/{niche}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@update',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@update',
        'as' => 'graveyard.niches.update',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/niches/{niche}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@destroy',
        'as' => 'graveyard.niches.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niches.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/niches/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@restore',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheController@restore',
        'as' => 'graveyard.niches.restore',
        'namespace' => NULL,
        'prefix' => '/graveyard/niches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-graves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@index',
        'as' => 'graveyard.permanent-graves.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-graves/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@create',
        'as' => 'graveyard.permanent-graves.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/permanent-graves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@store',
        'as' => 'graveyard.permanent-graves.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.search-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-graves/search-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@searchMembers',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@searchMembers',
        'as' => 'graveyard.permanent-graves.search-members',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.add-valid-member' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/permanent-graves/add-valid-member',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@addValidMember',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@addValidMember',
        'as' => 'graveyard.permanent-graves.add-valid-member',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-graves/{permanentGrave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@show',
        'as' => 'graveyard.permanent-graves.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-graves/{permanentGrave}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@edit',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@edit',
        'as' => 'graveyard.permanent-graves.edit',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'graveyard/permanent-graves/{permanentGrave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@update',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@update',
        'as' => 'graveyard.permanent-graves.update',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/permanent-graves/{permanentGrave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@destroy',
        'as' => 'graveyard.permanent-graves.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-graves.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/permanent-graves/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@restore',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveController@restore',
        'as' => 'graveyard.permanent-graves.restore',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-graves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@index',
        'as' => 'graveyard.temporary-graves.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-graves/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@create',
        'as' => 'graveyard.temporary-graves.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/temporary-graves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@store',
        'as' => 'graveyard.temporary-graves.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.search-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-graves/search-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@searchMembers',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@searchMembers',
        'as' => 'graveyard.temporary-graves.search-members',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-graves/{temporaryGrave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@show',
        'as' => 'graveyard.temporary-graves.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-graves/{temporaryGrave}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@edit',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@edit',
        'as' => 'graveyard.temporary-graves.edit',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'graveyard/temporary-graves/{temporaryGrave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@update',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@update',
        'as' => 'graveyard.temporary-graves.update',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/temporary-graves/{temporaryGrave}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@destroy',
        'as' => 'graveyard.temporary-graves.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-graves.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/temporary-graves/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@restore',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveController@restore',
        'as' => 'graveyard.temporary-graves.restore',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-graves',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/valid-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@index',
        'as' => 'graveyard.valid-members.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/valid-members/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@create',
        'as' => 'graveyard.valid-members.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/valid-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@store',
        'as' => 'graveyard.valid-members.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.search-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/valid-members/search-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@searchMembers',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@searchMembers',
        'as' => 'graveyard.valid-members.search-members',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.search-graves' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/valid-members/search-graves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@searchGraves',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@searchGraves',
        'as' => 'graveyard.valid-members.search-graves',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/valid-members/{validMember}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@show',
        'as' => 'graveyard.valid-members.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/valid-members/{validMember}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@edit',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@edit',
        'as' => 'graveyard.valid-members.edit',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'graveyard/valid-members/{validMember}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@update',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@update',
        'as' => 'graveyard.valid-members.update',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/valid-members/{validMember}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@destroy',
        'as' => 'graveyard.valid-members.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.valid-members.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/valid-members/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@restore',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ValidMemberController@restore',
        'as' => 'graveyard.valid-members.restore',
        'namespace' => NULL,
        'prefix' => '/graveyard/valid-members',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/service-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@index',
        'as' => 'graveyard.service-types.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/service-types/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@create',
        'as' => 'graveyard.service-types.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/service-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@store',
        'as' => 'graveyard.service-types.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/service-types/{serviceType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@show',
        'as' => 'graveyard.service-types.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/service-types/{serviceType}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@edit',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@edit',
        'as' => 'graveyard.service-types.edit',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'graveyard/service-types/{serviceType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@update',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@update',
        'as' => 'graveyard.service-types.update',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/service-types/{serviceType}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@destroy',
        'as' => 'graveyard.service-types.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/service-types/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@restore',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@restore',
        'as' => 'graveyard.service-types.restore',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.toggle-active' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/service-types/{serviceType}/toggle-active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@toggleActive',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@toggleActive',
        'as' => 'graveyard.service-types.toggle-active',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.service-types.api' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/service-types/service-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@getServiceTypes',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\ServiceTypeController@getServiceTypes',
        'as' => 'graveyard.service-types.api',
        'namespace' => NULL,
        'prefix' => '/graveyard/service-types',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-grave-bookings.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-grave-bookings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@index',
        'as' => 'graveyard.permanent-grave-bookings.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-grave-bookings.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-grave-bookings/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@create',
        'as' => 'graveyard.permanent-grave-bookings.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-grave-bookings.search-permanent-grave' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/permanent-grave-bookings/search-permanent-grave',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@searchPermanentGrave',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@searchPermanentGrave',
        'as' => 'graveyard.permanent-grave-bookings.search-permanent-grave',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-grave-bookings.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/permanent-grave-bookings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@store',
        'as' => 'graveyard.permanent-grave-bookings.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-grave-bookings.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permanent-grave-bookings/{permanentGraveBooking}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@show',
        'as' => 'graveyard.permanent-grave-bookings.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-grave-bookings.cancel' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/permanent-grave-bookings/{permanentGraveBooking}/cancel',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@cancel',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@cancel',
        'as' => 'graveyard.permanent-grave-bookings.cancel',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permanent-grave-bookings.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/permanent-grave-bookings/{permanentGraveBooking}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PermanentGraveBookingController@destroy',
        'as' => 'graveyard.permanent-grave-bookings.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/permanent-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-grave-bookings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@index',
        'as' => 'graveyard.temporary-grave-bookings.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-grave-bookings/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@create',
        'as' => 'graveyard.temporary-grave-bookings.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.search-members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-grave-bookings/search-members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@searchMembers',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@searchMembers',
        'as' => 'graveyard.temporary-grave-bookings.search-members',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/temporary-grave-bookings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@store',
        'as' => 'graveyard.temporary-grave-bookings.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.eligible-for-transfer' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-grave-bookings/eligible-for-transfer',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@eligibleForTransfer',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@eligibleForTransfer',
        'as' => 'graveyard.temporary-grave-bookings.eligible-for-transfer',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/temporary-grave-bookings/{temporaryGraveBooking}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@show',
        'as' => 'graveyard.temporary-grave-bookings.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.confirm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/temporary-grave-bookings/{temporaryGraveBooking}/confirm',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@confirm',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@confirm',
        'as' => 'graveyard.temporary-grave-bookings.confirm',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.cancel' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/temporary-grave-bookings/{temporaryGraveBooking}/cancel',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@cancel',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@cancel',
        'as' => 'graveyard.temporary-grave-bookings.cancel',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.temporary-grave-bookings.request-transfer' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/temporary-grave-bookings/{temporaryGraveBooking}/request-transfer',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@requestTransfer',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\TemporaryGraveBookingController@requestTransfer',
        'as' => 'graveyard.temporary-grave-bookings.request-transfer',
        'namespace' => NULL,
        'prefix' => '/graveyard/temporary-grave-bookings',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niche-transfers',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@index',
        'as' => 'graveyard.niche-transfers.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niche-transfers/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@create',
        'as' => 'graveyard.niche-transfers.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/niche-transfers',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@store',
        'as' => 'graveyard.niche-transfers.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.statistics' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niche-transfers/statistics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@statistics',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@statistics',
        'as' => 'graveyard.niche-transfers.statistics',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/niche-transfers/{nicheTransfer}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@show',
        'as' => 'graveyard.niche-transfers.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.approve' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/niche-transfers/{nicheTransfer}/approve',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@approve',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@approve',
        'as' => 'graveyard.niche-transfers.approve',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.reject' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/niche-transfers/{nicheTransfer}/reject',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@reject',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@reject',
        'as' => 'graveyard.niche-transfers.reject',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.complete' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/niche-transfers/{nicheTransfer}/complete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@complete',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@complete',
        'as' => 'graveyard.niche-transfers.complete',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.niche-transfers.cancel' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/niche-transfers/{nicheTransfer}/cancel',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@cancel',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\NicheTransferController@cancel',
        'as' => 'graveyard.niche-transfers.cancel',
        'namespace' => NULL,
        'prefix' => '/graveyard/niche-transfers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.payments.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/payments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@index',
        'as' => 'graveyard.payments.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/payments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.payments.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/payments/create/{bookingType}/{bookingId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@create',
        'as' => 'graveyard.payments.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/payments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.payments.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/payments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@store',
        'as' => 'graveyard.payments.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/payments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.payments.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/payments/{payment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@show',
        'as' => 'graveyard.payments.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/payments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.payments.balance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/payments/{payment}/balance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@balancePaymentForm',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@balancePaymentForm',
        'as' => 'graveyard.payments.balance',
        'namespace' => NULL,
        'prefix' => '/graveyard/payments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.payments.balance.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/payments/{payment}/balance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@storeBalancePayment',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@storeBalancePayment',
        'as' => 'graveyard.payments.balance.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/payments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.payments.receipt' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/payments/{payment}/receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@generateReceipt',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\PaymentController@generateReceipt',
        'as' => 'graveyard.payments.receipt',
        'namespace' => NULL,
        'prefix' => '/graveyard/payments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/grave-categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@index',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@index',
        'as' => 'graveyard.grave-categories.index',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/grave-categories/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@create',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@create',
        'as' => 'graveyard.grave-categories.create',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/grave-categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@store',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@store',
        'as' => 'graveyard.grave-categories.store',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/grave-categories/{graveCategory}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@show',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@show',
        'as' => 'graveyard.grave-categories.show',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/grave-categories/{graveCategory}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@edit',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@edit',
        'as' => 'graveyard.grave-categories.edit',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'graveyard/grave-categories/{graveCategory}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@update',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@update',
        'as' => 'graveyard.grave-categories.update',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'graveyard/grave-categories/{graveCategory}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@destroy',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@destroy',
        'as' => 'graveyard.grave-categories.destroy',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.grave-categories.restore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'graveyard/grave-categories/{id}/restore',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@restore',
        'controller' => 'Modules\\Graveyard\\Http\\Controllers\\GraveCategoryController@restore',
        'as' => 'graveyard.grave-categories.restore',
        'namespace' => NULL,
        'prefix' => '/graveyard/grave-categories',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'graveyard.permission-denied' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'graveyard/permission-denied',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
          3 => 'nocache',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:220:"function () {
        return \\Inertia\\Inertia::render(\'errors/PermissionDenied\', [
            \'message\' => \\request()->get(\'message\', \'\'),
            \'user\' => \\Illuminate\\Support\\Facades\\Auth::user()
        ]);
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009cd0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'graveyard.permission-denied',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EKquyEuaz7ujIIDO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/catholic-calendar',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'Modules\\Members\\Http\\Controllers\\CatholicCalendarController@index',
        'controller' => 'Modules\\Members\\Http\\Controllers\\CatholicCalendarController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::EKquyEuaz7ujIIDO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'permission-denied' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'permission-denied',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:200:"function () {
    return \\Inertia\\Inertia::render(\'errors/PermissionDenied\', [
        \'message\' => \\request()->get(\'message\', \'\'),
        \'user\' => \\Illuminate\\Support\\Facades\\Auth::user()
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000081a0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'permission-denied',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::iUkgDiG3LOySDse2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-419',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:32:"function () {
    \\abort(419);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009470000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::iUkgDiG3LOySDse2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NfSP3uGa9q6a9YDS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-404',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:32:"function () {
    \\abort(404);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000095c0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::NfSP3uGa9q6a9YDS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YxfF9nSdzDPUZX3e' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-500',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:32:"function () {
    \\abort(500);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000095f0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::YxfF9nSdzDPUZX3e',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ktebVoflnS8Oobab' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:77:"function (\\Illuminate\\Http\\Request $request) {
    return $request->user();
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009630000000000000000";}}',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::ktebVoflnS8Oobab',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3ciEUVuAlUCajfHP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:830:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'D:\\\\Projects\\\\salvation-admin\\\\vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Foundation\\\\Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000009e90000000000000000";}}',
        'as' => 'generated::3ciEUVuAlUCajfHP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:47:"D:\\Projects\\salvation-admin\\storage\\app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000009cb0000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
