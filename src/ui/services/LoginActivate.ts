import { Router, CanActivateFn, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { inject } from '@angular/core';
import { ApplicationState } from '.';

export const LoginActivate: CanActivateFn = (
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ) => {
    const applicationState = inject(ApplicationState);
    if(applicationState.isAuthenticated())
        return true;
    
    const router = inject(Router);
    return router.navigateByUrl("/login");
  };