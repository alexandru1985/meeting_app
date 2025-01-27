<script setup>
import { RouterLink } from 'vue-router'

import { logo } from '@/assets/brand/logo'
import { sygnet } from '@/assets/brand/sygnet'
import { AppSidebarNav } from '@/components/AppSidebarNav.js'
import { useSidebarStore } from '@/stores/sidebar.js'

const sidebar = useSidebarStore()
</script>

<template>
  <CSidebar
    class="border-end"
    colorScheme="dark"
    position="fixed"
    :unfoldable="sidebar.unfoldable"
    :visible="sidebar.visible"
    @visible-change="(value) => sidebar.toggleVisible(value)"
  >
    <CSidebarHeader class="border-bottom">
      <RouterLink custom to="/" v-slot="{ href, navigate }">
        <CSidebarBrand v-bind="$attrs" as="a" :href="href" @click="navigate">
        <a class="sidebar-brand" href="#/">
          <svg height="47" xmlns="http://www.w3.org/2000/svg" class="sidebar-brand-full" viewBox="25 0 532 116" role="img">
            <g>
              <g style="fill:none;stroke:currentColor;stroke-width:4;">
                <path d="M30 20 L100 20 L100 90 L30 90 Z30"></path>
                <text x="65" y="60" font-family="Arial" font-size="50" fill="currentColor" text-anchor="middle" dominant-baseline="middle" style="
                font-weight: 100 !important;">M</text>
              </g>
              <g style="fill: currentColor">
                <text x="120" y="74" font-family="Arial" font-size="50" fill="currentColor">Meeting-App</text>
              </g>
            </g>
          </svg>
        </a>
        <button type="button" class="btn btn-close d-lg-none" aria-label="Close" data-coreui-theme="dark"></button>
        </CSidebarBrand>
      </RouterLink>
      <CCloseButton class="d-lg-none" dark @click="sidebar.toggleVisible()" />
    </CSidebarHeader>
    <AppSidebarNav />
    <CSidebarFooter class="border-top d-none d-lg-flex">
      <CSidebarToggler @click="sidebar.toggleUnfoldable()" />
    </CSidebarFooter>
  </CSidebar>
</template>
