# 使用ScaffoldCommand快速生成Biz代码

## 基本用法

```bash
app/console biz:scaffold <tableName> <moduleName> <mode>

参数说明
  tableName                table_name, example: user, user_profile (小写)
  moduleName               module_name, example: User（首字母大写）
  mode                     DSC: D=dao,S=service,C=Controller (大写)
```

示例

```
# 新建了一张user_token表后，希望在src/Biz/User目录下生成Dao、Service代码
app/console biz:scaffold user_token User DS
```

## 调整生成的代码

由于自动生成的代码比较呆板，使用之前我们需要进行微调

1. 调整DaoImpl文件中的conditions字段数组，包括字段缩进、删除不必要的条件
2. 调整ServiceImpl文件中的filterCreateUserTokenFields方法，按照实际情况修改$requiredFields和$default信息
3. 调整ServiceImpl文件中的filterUpdateUserTokenFields方法，按照实际情况修改允许更新的$fields信息
4. 删除不必要的$this->dispatchEvent
5. 删除不必要的$this->getLogService()->info