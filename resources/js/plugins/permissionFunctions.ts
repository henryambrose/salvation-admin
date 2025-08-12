export default {
    install(app: any) {
      // Access permissions from Inertia page props
      const can = (permission: string) => {
        if (!app.config.globalProperties.$page.props.auth || !app.config.globalProperties.$page.props.auth.permissions) {
            return false
        }
        const permissions = app.config.globalProperties.$page.props.auth.permissions || []
        return permissions.includes(permission)
      }

      const formatDate = (dateStr: string) => {
        const date = new Date(dateStr)
        return date.toLocaleDateString('en-IN', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
        })
      }

      // Register functions as global properties
      app.config.globalProperties.$can = can
      app.config.globalProperties.$formatDate = formatDate
    }
}
