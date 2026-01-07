-- Enable UUID extension
create extension if not exists "uuid-ossp";

-- Create Expenses Table
create table public.expenses (
  id uuid default uuid_generate_v4() primary key,
  user_id uuid references auth.users not null,
  amount decimal(10,2) not null,
  category text not null,
  date date not null,
  merchant text,
  notes text,
  created_at timestamp with time zone default timezone('utc'::text, now()) not null
);

-- RLS for Expenses
alter table public.expenses enable row level security;

create policy "Users can view their own expenses"
on public.expenses for select
using (auth.uid() = user_id);

create policy "Users can insert their own expenses"
on public.expenses for insert
with check (auth.uid() = user_id);

create policy "Users can update their own expenses"
on public.expenses for update
using (auth.uid() = user_id);

create policy "Users can delete their own expenses"
on public.expenses for delete
using (auth.uid() = user_id);


-- Create Budgets Table
create table public.budgets (
  id uuid default uuid_generate_v4() primary key,
  user_id uuid references auth.users not null,
  category text not null,
  amount_limit decimal(10,2) not null,
  month text not null, -- Format 'YYYY-MM'
  created_at timestamp with time zone default timezone('utc'::text, now()) not null,
  -- Ensure one budget per category per month per user
  unique(user_id, category, month)
);

-- RLS for Budgets
alter table public.budgets enable row level security;

create policy "Users can view their own budgets"
on public.budgets for select
using (auth.uid() = user_id);

create policy "Users can insert their own budgets"
on public.budgets for insert
with check (auth.uid() = user_id);

create policy "Users can update their own budgets"
on public.budgets for update
using (auth.uid() = user_id);

create policy "Users can delete their own budgets"
on public.budgets for delete
using (auth.uid() = user_id);


-- Create Categories Table
create table public.categories (
  id uuid default uuid_generate_v4() primary key,
  user_id uuid references auth.users not null,
  name text not null,
  created_at timestamp with time zone default timezone('utc'::text, now()) not null,
  unique(user_id, name)
);

-- RLS for Categories
alter table public.categories enable row level security;

create policy "Users can view their own categories"
on public.categories for select
using (auth.uid() = user_id);

create policy "Users can insert their own categories"
on public.categories for insert
with check (auth.uid() = user_id);

create policy "Users can delete their own categories"
on public.categories for delete
using (auth.uid() = user_id);


-- Create Accounts Table
create table public.accounts (
  id uuid default uuid_generate_v4() primary key,
  user_id uuid references auth.users not null,
  name text not null,
  type text default 'General', -- Bank, Cash, etc.
  balance decimal(10,2) default 0.00 not null,
  created_at timestamp with time zone default timezone('utc'::text, now()) not null
);

-- RLS for Accounts
alter table public.accounts enable row level security;

create policy "Users can view their own accounts"
on public.accounts for select
using (auth.uid() = user_id);

create policy "Users can insert their own accounts"
on public.accounts for insert
with check (auth.uid() = user_id);

create policy "Users can update their own accounts"
on public.accounts for update
using (auth.uid() = user_id);

create policy "Users can delete their own accounts"
on public.accounts for delete
using (auth.uid() = user_id);


-- Update Expenses Table (Add account_id)
alter table public.expenses 
add column account_id uuid references public.accounts(id) on delete set null;


-- Function to Update Account Balance automatically
create or replace function update_account_balance()
returns trigger as $$
begin
  -- IF INSERTING: Deduct amount from new account
  if (TG_OP = 'INSERT') then
    if NEW.account_id is not null then
      update public.accounts 
      set balance = balance - NEW.amount
      where id = NEW.account_id;
    end if;
    return NEW;
  
  -- IF DELETING: Refund amount to old account
  elsif (TG_OP = 'DELETE') then
    if OLD.account_id is not null then
      update public.accounts 
      set balance = balance + OLD.amount
      where id = OLD.account_id;
    end if;
    return OLD;

  -- IF UPDATING:
  elsif (TG_OP = 'UPDATE') then
    -- 1. Refund old amount to old account
    if OLD.account_id is not null then
      update public.accounts 
      set balance = balance + OLD.amount
      where id = OLD.account_id;
    end if;
    
    -- 2. Deduct new amount from new account
    if NEW.account_id is not null then
      update public.accounts 
      set balance = balance - NEW.amount
      where id = NEW.account_id;
    end if;
    return NEW;
  end if;
  return null;
  return null;
end;
$$ language plpgsql; -- Removed security definer to respect RLS

-- Trigger for Expenses
create trigger on_expense_change
after insert or update or delete on public.expenses
for each row execute procedure update_account_balance();


-- Function to Check Account Ownership
create or replace function check_expense_account_ownership()
returns trigger as $$
declare
  is_owner boolean;
begin
  -- Only check if account_id is provided
  if NEW.account_id is not null then
    select exists (
      select 1 from public.accounts
      where id = NEW.account_id
      and user_id = auth.uid()
    ) into is_owner;

    if not is_owner then
      raise exception 'Invalid account_id: You do not own this account.';
    end if;
  end if;
  return NEW;
end;
$$ language plpgsql security definer;

-- Trigger to Enforce Account Ownership
create trigger before_expense_change_ownership
before insert or update on public.expenses
for each row execute procedure check_expense_account_ownership();
