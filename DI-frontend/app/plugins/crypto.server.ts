import { createCipheriv, createDecipheriv, randomBytes, scryptSync } from 'crypto'
// import { defineNitroPlug, useRuntimeConfig } from '#app'



export default defineNuxtPlugin(() => {
    const ALGORITHM = 'aes-256-gcm'
    const IV_LENGTH = 12 // Recommended IV size for GCM
    const AUTH_TAG_LENGTH = 16
    const config = useRuntimeConfig()
    const password = config.private.crypto_pass

    if (!password) {
        throw new Error('Missing crypto_pass in runtime config')
    }

    // Derive a 32-byte key using scrypt
    const key = scryptSync(password, 'salt', 32) // Use a static or dynamic salt appropriately

    const $encrypt = (message: string): string => {
        const iv = randomBytes(IV_LENGTH)
        const cipher = createCipheriv(ALGORITHM, key, iv)

        const encrypted = Buffer.concat([cipher.update(message, 'utf8'), cipher.final()])
        const authTag = cipher.getAuthTag()

        // Encode iv + encrypted + authTag as base64
        const result = Buffer.concat([iv, encrypted, authTag]).toString('base64')
        return result
    }

    const $decrypt = (encryptedData: string): string => {
        const data = Buffer.from(encryptedData, 'base64')

        const iv = data.subarray(0, IV_LENGTH)
        const authTag = data.subarray(data.length - AUTH_TAG_LENGTH)
        const encrypted = data.subarray(IV_LENGTH, data.length - AUTH_TAG_LENGTH)

        const decipher = createDecipheriv(ALGORITHM, key, iv)
        decipher.setAuthTag(authTag)

        const decrypted = Buffer.concat([decipher.update(encrypted), decipher.final()])
        return decrypted.toString('utf8')
    }

    return {
        provide: {
        encrypt: $encrypt,
        decrypt: $decrypt,
        },
    }
})
