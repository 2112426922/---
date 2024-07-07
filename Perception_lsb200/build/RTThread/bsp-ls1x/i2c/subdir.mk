#
# Auto-Generated file. Do not edit!
#

# Add inputs and outputs from these tool invocations to the build variables
C_SRCS += \
../RTThread/bsp-ls1x/i2c/drv_ads1015.c \
../RTThread/bsp-ls1x/i2c/drv_mcp4725.c \
../RTThread/bsp-ls1x/i2c/drv_pca9557.c \
../RTThread/bsp-ls1x/i2c/drv_rx8010.c

OBJS += \
./RTThread/bsp-ls1x/i2c/drv_ads1015.o \
./RTThread/bsp-ls1x/i2c/drv_mcp4725.o \
./RTThread/bsp-ls1x/i2c/drv_pca9557.o \
./RTThread/bsp-ls1x/i2c/drv_rx8010.o

C_DEPS += \
./RTThread/bsp-ls1x/i2c/drv_ads1015.d \
./RTThread/bsp-ls1x/i2c/drv_mcp4725.d \
./RTThread/bsp-ls1x/i2c/drv_pca9557.d \
./RTThread/bsp-ls1x/i2c/drv_rx8010.d

# Each subdirectory must supply rules for building sources it contributes
RTThread/bsp-ls1x/i2c/%.o: ../RTThread/bsp-ls1x/i2c/%.c
	@echo 'Building file: $<'
	@echo 'Invoking: SDE Lite C Compiler'
	D:/LoongIDE/mips-2011.03/bin/mips-sde-elf-gcc.exe -mips32 -G0 -EL -msoft-float -DLIB_LVGL -DLS1B -DOS_RTTHREAD  -O0 -g -Wall -c -fmessage-length=0 -pipe -I"../" -I"../include" -I"$(GCC_SPECS)/$(OS)/lvgl-7.0.1" -I"../ls1x-drv/include" -I"../RTThread/bsp-ls1x" -I"../RTThread/components/dfs/include" -I"../RTThread/components/drivers/include" -I"../RTThread/components/finsh" -I"../RTThread/components/libc/time" -I"../RTThread/include" -I"../RTThread/port/include" -I"../RTThread/port/mips" -I"../src/buzzer" -I"../src/gpio_uart" -I"../src/hdc2080" -I"../src/key" -I"../src/lvgl_ui" -I"../src/other" -I"../src/relay" -I"../src/SPL06_007" -I"../src/tsl2561fn" -I"../src/user_drv" -MMD -MP -MF"$(@:%.o=%.d)" -MT"$(@)" -o "$@" "$<"
	@echo 'Finished building: $<'
	@echo ' '

