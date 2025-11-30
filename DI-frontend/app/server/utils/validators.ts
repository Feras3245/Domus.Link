import { z } from 'zod';

export const emailValidator = z.string().email().safeParse;

export const nameValidator = z.string()
.min(3)
.max(100)
.regex(/^([a-zA-ZäöüßÄÖÜ\-]+[a-zA-ZäöüßÄÖÜ\-\s]*){3,}$/)
.safeParse;


export const passwordValidator = z.string()
.min(10)
.max(100)
.regex(/^[a-zA-ZäöüßÄÖÜ0-9!@#$%^&*()_\-+=\[\]{};:'",.<>/?\\|`~]+$/)
.refine(password => !/\s/.test(password))
.refine(password => (password.match(/[0-9]/g) || []).length >= 2)
.refine(password => /[!@#$%^&*()_\-+=\[\]{};:'",.<>/?\\|`~]/.test(password))
.safeParse;