import { inject } from '@angular/core';
import { HttpInterceptorFn } from '@angular/common/http';
import { ApplicationState } from './'

var applicationState : ApplicationState;
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  if(req.headers.get("Authorization") != undefined)
    return next(req);

  const authService = inject(ApplicationState);
  if(authService.User == null)
    return next(req);

  const cloned = req.clone({
    headers: req.headers.set("Authorization",
        "Bearer " + authService.User.token)
  });

  return next(cloned);
};
