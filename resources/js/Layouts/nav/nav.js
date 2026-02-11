import { reactive } from "vue";

export function getMenuItems(page) {
  const menuItems = reactive([
    { id: "dashboard", name: "Dashboard", icon: "📊", routeTo: "dashboard" }
  ]);

  if (page.auth && page.auth.user.usertype === 2) {
    menuItems.push({
      id: "nesa",
      name: "Admin Nesa",
      icon: "📈",
      children: [
        {
          id: "nesa-dashboard",
          name: "Dashboard",
          icon: "📊",
          routeTo: "nesa.get.dashboard"
        }
      ]
    });

    menuItems.push({
      id: "bad-order",
      name: "Admin Bad Order",
      icon: "🚚",
      children: [
        {
          id: "bo-dashboard",
          name: "Dashboard",
          icon: "📊",
          routeTo: "nesa.get.badorder"
        },
      ]
    });
  } else if (page.auth && page.auth.user.usertype === 3) {
    menuItems.push({
      id: "nesa",
      name: "Nesa",
      icon: "📈",
      children: [
        {
          id: "nesa-approved",
          name: "Approved",
          icon: "✅",
          routeTo: "nesa.get.approved.nesa"
        }
      ]
    });

    menuItems.push({
      id: "bad-order",
      name: "Bad Order",
      icon: "🚚",
      children: [
        {
          id: "bo-dashboard",
          name: "Dashboard",
          icon: "📊",
          routeTo: "nesa.get.badorder"
        }
      ]
    });
  } else if (page.auth && page.auth.user.usertype === 5) {
    menuItems.push({
      id: "nesa",
      name: "Nesa",
      icon: "📈",
      children: [
        {
          id: "nesa-list",
          name: "Nesa List",
          icon: "📋",
          routeTo: "nesa.get.list"
        },
        {
          id: "nesa-consolidated",
          name: "Consolidated",
          icon: "📑",
          routeTo: "nesa.get.consolidated"
        },
        {
          id: "nesa-approved",
          name: "Approved",
          icon: "✅",
          routeTo: "nesa.get.approved.nesa"
        }
      ]
    });
  }else if (page.auth && page.auth.user.usertype === 6) {
    menuItems.push({
      id: "nesa",
      name: "Nesa",
      icon: "📈",
      children: [
        {
          id: "nesa-approval",
          name: "Pending For Approval",
          icon: "📋",
          routeTo: "nesa.get.pending.for.approval"
        },
        {
          id: "approved-list",
          name: "Approved List",
          icon: "📑",
          routeTo: "nesa.get.approved.nesa"
        },
      ]
    });
  }

  menuItems.push(
    {
      id: "masterfile",
      name: "MasterFile",
      icon: "⚙️",
      routeTo: "admin.masterfile.index"
    },
    {
      id: "profile",
      name: "Profile",
      icon: "💂‍♂️",
      routeTo: "admin.viewProfile"
    },
    { id: "reports", name: "Reports", icon: "📝", routeTo: "admin.reports" }
  );


  return menuItems;
}
